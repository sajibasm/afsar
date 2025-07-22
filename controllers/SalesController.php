<?php

namespace app\controllers;

use app\components\CommonUtility;
use app\components\EmailService;
use app\components\FlashMessage;
use app\components\InvoiceGenerator;
use app\components\ProductStoreUtility;
use app\components\ProductUtility;
use app\components\StoreSelectionHelper;
use app\components\SystemSettings;
use app\components\Utility;
use app\models\BankReconciliation;
use app\models\Client;
use app\models\ClientPaymentDetails;
use app\models\ClientTransactionSummary;
use app\models\PaymentType;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\SalesDraft;
use app\models\SalesDraftSearch;
use app\models\SalesSearch;
use app\models\SalesSMSQueue;
use app\models\Transport;
use app\services\CartService;
use app\services\ClientFinancialService;
use app\services\InvoiceApproveService;
use app\services\InvoiceCreateService;
use app\services\InvoiceDeleteService;
use kartik\form\ActiveForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * SalesController implements the CRUD actions for Sales model.
 */
class SalesController extends Controller
{
    public $isCustomPriceEnable = true;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['invoice-lookup'],  // ✅ Public access
                        'allow' => true,
                        'roles' => ['?'],  // Guest users (no login required)
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],  // ✅ All other actions require login
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'approve' => ['POST'],
                ],
            ],
        ];
    }

    public function actionGetBrandListByItem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $output = [];
        $parents = Yii::$app->request->post('depdrop_parents', []);

        if (!empty($parents[0])) {
            $itemId = (int) $parents[0];
            $brands = ProductUtility::getBrandListByItem($itemId);

            foreach ($brands as $brand) {
                $output[] = [
                    'id'   => $brand->brand_id,
                    'name' => $brand->brand_name
                ];
            }

            return [
                'output'  => $output,
                'selected' => ''
            ];
        }

        return [
            'output'  => '',
            'selected' => ''
        ];
    }

    public function actionGetSizeListByBrand()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $output = [];

        $request = Yii::$app->request->post('depdrop_parents', []);

        if (count($request) >= 2) {
            $itemId = (int) $request[0];
            $brandId = (int) $request[1];

            $sizes = ProductUtility::getSizeListByBrand($itemId, $brandId);

            foreach ($sizes as $size) {
                $output[] = [
                    'id'   => $size->size_id,
                    'name' => $size->size_name
                ];
            }

            return [
                'output'  => $output,
                'selected' => ''
            ];
        }

        return [
            'output'  => '',
            'selected' => ''
        ];
    }

    public function actionGetProductPrice()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request->post();
        $output = [];

        if (!Yii::$app->request->isPost || empty($request['depdrop_parents'][0])) {
            return ['output' => '', 'selected' => ''];
        }

        $sizeId = (int) $request['depdrop_parents'][0];
        $stockPrice = ProductUtility::getProductStockPrice($sizeId);

        if (!$stockPrice) {
            return ['output' => '', 'selected' => ''];
        }

        $output[] = [
            'id'   => $stockPrice->wholesale_price,
            'name' => 'Wholesale: ' . number_format($stockPrice->wholesale_price, 2)
        ];

        $output[] = [
            'id'   => $stockPrice->retail_price,
            'name' => 'Retail: ' . number_format($stockPrice->retail_price, 2)
        ];

        if (!empty($this->isCustomPriceEnable)) {
            $output[] = [
                'id'   => 'custom',
                'name' => 'Custom Price'
            ];
        }

        return [
            'output'  => $output,
            'selected' => ''
        ];
    }

    public function actionCheckAvailableProduct()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request->post();

        if (!Yii::$app->request->isPost || empty($request['size_id']) || empty($request['outletId'])) {
            return [
                'error'   => true,
                'message' => 'Invalid request or missing parameters.',
            ];
        }

        try {
            $sizeId = (int) $request['size_id'];
            $storeId = $request['outletId'];

            if (!$storeId || !$sizeId) {
                return [
                    'error'   => true,
                    'message' => 'Invalid size or outlet.',
                ];
            }

            return ProductStoreUtility::getAvailableProductInfo($sizeId, $storeId);

        } catch (\Throwable $e) {
            Yii::error("CheckAvailableProduct Error: " . $e->getMessage(), __METHOD__);
            return [
                'error'   => true,
                'message' => 'Something went wrong while checking product availability.',
            ];
        }
    }

    private function getAvailableQty($sizeId, $storeId)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ProductStoreUtility::getAvailableProductInfo($sizeId, $storeId);
    }

    public function actionCustomerDetails()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request->post();
            if (isset($request['Sales']['client_id'])) {
                $client = Client::findOne($request['Sales']['client_id']);
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $client;
            }
        }
    }

    public function actionInvoiceLookup($token = null)
    {
        if (!$token) {
            throw new BadRequestHttpException('Missing token.');
        }

        try {
            $decrypted = Utility::decrypt($token);
            list($salesId, $clientId, $expiryTimestamp) = explode('|', $decrypted);

            if (time() > (int) $expiryTimestamp) {
                throw new BadRequestHttpException('The invoice link has expired.');
            }

            $model = Sales::findOne(['sales_id' => $salesId, 'client_id' => $clientId]);
            if (!$model) {
                throw new NotFoundHttpException('Invoice not found or access denied.');
            }

            return $this->sendInvoicePdf($salesId);

        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid or expired access token.');
        }
    }

    public function actionPrint($id)
    {
        $salesId = Utility::decrypt($id);
        return $this->sendInvoicePdf($salesId);
    }

    private function sendInvoicePdf($salesId)
    {
        $filename = Yii::getAlias('@runtime/') . "invoice_{$salesId}.pdf";
        InvoiceGenerator::salesInvoice($salesId, $filename);

        if (!file_exists($filename)) {
            throw new NotFoundHttpException('Invoice file could not be generated.');
        }
        return Yii::$app->response->sendFile($filename, "Sales Invoice {$salesId}.pdf", [
            'mimeType' => 'application/pdf',
            'inline' => true,
        ])->on(Response::EVENT_AFTER_SEND, function () use ($filename) {
            @unlink($filename);
        });
    }

    public function actionNotification()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            return [
                'success' => false,
                'message' => 'Invalid request method.',
                'data' => null
            ];
        }

        $response = Yii::$app->json;
        $id = Yii::$app->request->post('id');

        if (empty($id)) {
            return $response->error('Invalid request: missing ID.');
        }

        $model = $this->findModel(Utility::decrypt($id));
        if (!$model) {
            return $response->error('Sales record not found.');
        }

        $customerEmail = $model->client->email;
        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            return $response->error('Customer email is invalid or missing.');
        }

        // Generate secure token and public invoice link
        $expiryTimestamp = time() + (3 * 24 * 60 * 60); // 3 days
        $tokenString = "{$model->sales_id}|{$model->client_id}|{$expiryTimestamp}";
        $secureToken = Utility::encrypt($tokenString);
        $publicUrl = Url::to(['sales/invoice-lookup', 'token' => $secureToken], true);

        // Generate PDF invoice
        $pdfPath = Yii::getAlias('@runtime/') . "invoice_{$model->sales_id}.pdf";
        InvoiceGenerator::salesInvoice($model->sales_id, $pdfPath);

        if (!file_exists($pdfPath)) {
            return $response->error('Invoice PDF could not be generated.');
        }

        // Call EmailService to send email
        /** @var EmailService $emailService */
        $emailService = new EmailService();
        $result = $emailService->sendCustomerEmail(
            $customerEmail,
            'invoice/invoice-notification', // HTML view
            [
                'clientName' => $model->client->client_name,
                'publicUrl' => $publicUrl
            ],
            "Your " . SystemSettings::getStoreName() . " Invoice #{$model->sales_id} is Ready – Thank You for Shopping!",
            $pdfPath,
            "Invoice_{$model->sales_id}.pdf"
        );

        @unlink($pdfPath); // Clean up the file after sending
        return $result;
    }

    public function actionTransport($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Sales');
            $transport = Transport::findOne($data['transport_id']);

            if (!$transport) {
                return [
                    'output' => '',
                    'message' => 'Invalid transport selected.'
                ];
            }

            $model->setAttribute('transport_id', $data['transport_id']);
            $model->setAttribute('transport_name', $transport->transport_name);
            $model->setAttribute('tracking_number', $data['tracking_number']);
            $model->setUserAction("Transport Added");

            if ($model->save()) {
                // Optional Notifications
                return [
                    'output' => $model->transport_name . "\nTracking ({$model->tracking_number})",
                    'message' => 'Transport info saved successfully.'
                ];
            } else {
                return [
                    'output' => '',
                    'message' => ActiveForm::validate($model),
                ];
            }
        }

        return [
            'output' => '',
            'message' => 'Invalid request method.'
        ];
    }

    /**
     * @return string|void
     * @throws NotFoundHttpException
     */
    public function actionDetails()
    {

        if (isset($_POST['expandRowKey'])) {
            $salesId = $_POST['expandRowKey'];
            $model = $this->findModel($salesId);
            $salesDataProvider = new ActiveDataProvider([
                'query'      => SalesDetails::find()->where(['sales_id' => $salesId]),
                'pagination' => false,
            ]);

            $bankReconciliations = BankReconciliation::find()
                ->where(['invoice_id' => $salesId, 'customer_id' => $model->client_id])
                ->all();


            $clientPaymentDetails = ClientPaymentDetails::find()
                ->where(['sales_id' => $salesId, 'client_id' => $model->client_id])
                ->all();

            $paymentDetailsIds = array_column($clientPaymentDetails, 'client_payment_details_id');

            // Extract all reconciliation IDs
            $reconciliationIds = array_column($bankReconciliations, 'id');

            // Build query with both conditions
            $query = ClientTransactionSummary::find()
                ->where([
                    'or',
                    [
                        'reference_id' => $salesId,
                        'reference_table' => ClientFinancialService::REF_TABLE_SALE
                    ],
                    [
                        'and',
                        ['in', 'reference_id', $paymentDetailsIds],
                        ['reference_table' => ClientFinancialService::REF_TABLE_PAYMENT_SETTLEMENT]
                    ],
                    [
                        'and',
                        ['in', 'reference_id', $reconciliationIds],
                        ['reference_table' => ClientFinancialService::REF_TABLE_RECONCILIATION]
                    ],
                ])
                ->orderBy('id ASC');

            $clientTransactionSummary = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]);


            return $this->renderAjax('details', [
                'model' => $model,
                'salesId' => $salesId,
                'salesDataProvider' => $salesDataProvider,
                'clientTransactionSummary' => $clientTransactionSummary,
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array|void
     * @throws NotFoundHttpException
     */
    public function actionApprove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            if (empty($id)) {
                return [
                    'success' => false,
                    'message' => 'Invalid request: missing ID.',
                    'data' => null
                ];
            }

            $model = $this->findModel(Utility::decrypt($id));
            $model->setUserAction("Approved");
            $model->updated_by = Yii::$app->user->id;

            $invoiceApproveService = new InvoiceApproveService();
            $result = $invoiceApproveService->approve($model);
            // Prepare common response payload
            $response = [
                'success' => $result['success'],
                'message' => $result['message'] ?? ($result['success'] ? 'Sales invoice approval failed.' : 'Sales invoice approved successfully.'),
                'data' => [
                    'salesId' => $model->sales_id,
                    'salesType' => $model->type
                ]
            ];

            // If approval succeeded → send notifications
            if ($result['success']) {
                if ($model->type == Sales::TYPE_SALES) {
                } elseif (SystemSettings::invoiceUpdateNotificationEmail() && !empty($model->client->email)) {
                    // Yii::$app->queue->push(new SalesUpdateEmailQueue(['salesId' => $model->sales_id]));
                }
            }

            return $response;
        }
    }

    public function actionAddCartItem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return [
                'success' => false,
                'message' => 'Invalid request method.',
                'type' => 'invalid',
            ];
        }

        $postData = Yii::$app->request->post();
        $cartService = new CartService();
        return $cartService->addCartItem($postData); // already returns consistent success/false structure
    }

    public function actionUpdateCartItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isPost && Yii::$app->request->post('hasEditable')){
            $cartService = new CartService();
            return $cartService->updateCartItem(Yii::$app->request->post());
        }else{
            return ['output' => '', 'message' => 'Invalid request'];
        }
    }

    public function actionRemoveCartItem()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        if (empty($id)) {
            return [
                'success' => false,
                'message' => 'Invalid request: missing item ID.',
            ];
        }

        $modelId = Utility::decrypt($id);
        $model = SalesDraft::findOne($modelId);

        if (!$model) {
            return [
                'success' => false,
                'message' => 'Item not found.',
            ];
        }

        if ($model->type == SalesDraft::TYPE_INSERT) {
            if ($model->delete()) {
                return [
                    'success' => true,
                    'message' => 'Item has been successfully deleted.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete the item. Please try again.',
                'errors'  => $model->getErrors(),
            ];
        }

        if ($model->type == SalesDraft::TYPE_UPDATE) {
            $model->type = SalesDraft::TYPE_UPDATE_DELETED;
            if ($model->save()) {
                return [
                    'success' => true,
                    'message' => 'Item has been successfully deleted.',
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to delete the item. Please try again.',
                'errors'  => $model->getErrors(),
            ];
        }


        return [
            'success' => false,
            'message' => 'This item type cannot be deleted.',
        ];
    }

    /**
     * Applies VAT and AIT tax calculations to the given Sales model based on draft total.
     *
     * @param Sales $model The sales model to update
     * @return void
     */
    private function applyTaxCalculations(Sales $model, $totalAmount): void
    {
        $vatPercent = SystemSettings::getVAT();     // e.g., 15
        $aitPercent = SystemSettings::getAIT();     // e.g., 3

        $vatAmount = ($totalAmount * $vatPercent) / 100;
        $aitAmount = ($totalAmount * $aitPercent) / 100;

        $model->total_amount = $totalAmount + $vatAmount + $aitAmount;
        $model->vat_amount = $vatAmount;
        $model->advance_income_tax_amount = $aitAmount;
    }


    public function actionCreate()
    {

        $storeId = StoreSelectionHelper::handleStoreSelection('create');
        if ($storeId instanceof \yii\web\Response || is_string($storeId)) {
            return $storeId;  // Return early if redirected or rendered
        }

        $store = $storeId;
        $model = new Sales();
        $model->outletId = $store;
        $model->setScenario('Sales');
        $model->user_id = Yii::$app->user->getId();


        $totalAmount = SalesDraft::getTotal(null, SalesDraft::TYPE_INSERT, Yii::$app->user->getId()); // e.g., 1000

        /*
         * Applies VAT and AIT tax calculations to the given Sales model based on draft total.
         */
        $this->applyTaxCalculations($model, $totalAmount);

        $model->received_amount = 0;
        $model->reconciliation_amount = 0;
        $model->sales_return_amount = 0;
        $model->paid_amount = 0;
        $model->due_amount = $model->total_amount;
        $model->discount_amount = 0;
        $model->payment_type = CommonUtility::getDefaultPaymentTypeId(PaymentType::TYPE_CASH);

        $salesDraft = new SalesDraft();
        $salesDraft->user_id = Yii::$app->user->getId();
        $salesDraft->outletId = $store;
        $salesDraft->type = SalesDraft::TYPE_INSERT;

        $salesDraftSearchModel = new SalesDraftSearch();
        $salesDraftSearchModel->type = SalesDraft::TYPE_INSERT;
        $salesDraftSearchModel->outletId = $store;
        $salesDraftSearchModel->user_id = Yii::$app->user->getId();
        $salesDraftDataProvider = $salesDraftSearchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                $model->setUserAction("Sales Created");
                $model->received_amount = $model->paid_amount;
                $model->status = Sales::STATUS_PENDING;

                if (empty($model->client_name)) {
                    $model->addError('client_name', 'Customer Name cannot be blank.');
                }

                if ($model->paid_amount > $model->total_amount) {
                    $model->addError('paid_amount', 'should be less or equal to total amount');
                }

                if ($model->paymentTypeModel->type == PaymentType::TYPE_DEPOSIT && (empty($model->bank) || empty($model->branch))) {
                    $model->bank = 0;
                    $model->branch = 0;
                    $model->payment_type = 0;
                    $model->addError('bank', 'Bank Can\'t be Empty');
                    $model->addError('branch', 'Branch Can\'t be Empty');
                } else {
                    if ($model->validate()) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($model->save()) {
                                $invoiceService = new InvoiceCreateService();
                                if ($invoiceService->finalizeCartForInvoice($model)) {
                                    $transaction->commit();
                                    $message = 'Sales invoice #' . trim($model->sales_id) . ' has been created and is awaiting approval.';
                                    FlashMessage::setMessage($message, 'Sales Created', 'success');
                                    return $this->redirect(['index']);
                                } else {
                                    $transaction->rollBack();

                                }
                            }
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            throw $e;
                        }
                    }
                }
        }

        return $this->render('new/create', [
            'model' => $model,
            'salesDraft' => $salesDraft,
            'salesDraftDataProvider' => $salesDraftDataProvider,
        ]);

    }

    public function actionUpdate($sales_id = null)
    {
        $cartService = new CartService();

        $id = Utility::decrypt($sales_id);
        $model = $this->findModel($id);

        $cartService = new CartService();
        if(!$cartService->moveProductsToDraft($id)){
            return $this->redirect(['index']);
        }

//        $model = new Sales();
        $model->setScenario('Sales');
        $model->user_id = Yii::$app->user->getId();
        $model->sales_id = $id;


        $totalAmount = SalesDraft::getTotal(null, [SalesDraft::TYPE_UPDATE_ADDED,SalesDraft::TYPE_UPDATE_MODIFIED, SalesDraft::TYPE_UPDATE], Yii::$app->user->getId()); // e.g., 1000

        /*
        * Applies VAT and AIT tax calculations to the given Sales model based on draft total.
        */
        $this->applyTaxCalculations($model, $totalAmount);
        $model->due_amount = $model->total_amount - $model->paid_amount;

        $salesDraft = new SalesDraft();
        $salesDraft->user_id = Yii::$app->user->getId();
        $salesDraft->outletId =  $model->outletId;
        $salesDraft->sales_id = $model->sales_id;

        $salesDraftSearchModel = new SalesDraftSearch();
        $salesDraftSearchModel->outletId = $model->outletId;
        $salesDraftSearchModel->type = [SalesDraft::TYPE_UPDATE, SalesDraft::TYPE_UPDATE_ADDED, SalesDraft::TYPE_UPDATE_MODIFIED, SalesDraft::TYPE_UPDATE_DELETED];
        $salesDraftSearchModel->user_id = Yii::$app->user->getId();
        $salesDraftDataProvider = $salesDraftSearchModel->searchUpdate(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->setUserAction("Sales Created");
            $model->received_amount = $model->paid_amount;
            $model->status = Sales::STATUS_PENDING;

            if (empty($model->client_name)) {
                $model->addError('client_name', 'Customer Name cannot be blank.');
            }

            if ($model->paid_amount > $model->total_amount) {
                $model->addError('paid_amount', 'should be less or equal to total amount');
            }

            if ($model->paymentTypeModel->type == PaymentType::TYPE_DEPOSIT && (empty($model->bank) || empty($model->branch))) {
                $model->bank = 0;
                $model->branch = 0;
                $model->payment_type = 0;
                $model->addError('bank', 'Bank Can\'t be Empty');
                $model->addError('branch', 'Branch Can\'t be Empty');
            } else {
                if ($model->validate()) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($model->save()) {
                            $message = 'Sales invoice #' . trim($model->sales_id) . ' has been updated and is awaiting approval.';
                            if ($cartService->isCartUnchanged($model->sales_id, $model->user_id, $model->outletId)) {
                                $transaction->commit();
                                FlashMessage::setMessage($message, 'Sales Updated', 'success');
                                return $this->redirect(['index']);
                            }else{
                                $invoiceService = new InvoiceCreateService();
                                if ($invoiceService->finalizeUpdateCartForInvoice($model)) {
                                    $transaction->commit();
                                    FlashMessage::setMessage($message, 'Sales Updated', 'success');
                                    return $this->redirect(['index']);
                                } else {
                                    $transaction->rollBack();

                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                }
            }
        }

        return $this->render('update/create', [
            'model' => $model,
            'salesDraft' => $salesDraft,
            'salesDraftDataProvider' => $salesDraftDataProvider,
        ]);

    }

    public function actionCancelSalesInvoice()
    {
        SalesDraft::deleteAll(['user_id' => Yii::$app->user->getId()]);
        $this->redirect('index');
    }

    /**
     * Deletes an existing Sales model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $model = $this->findModel(Utility::decrypt($id));
            return (new InvoiceDeleteService())->delete($model);
        }else{
            return ['success' => false, 'message' => "Invalid request"];
        }

    }


    protected function findModel($id)
    {
        if (($model = Sales::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
