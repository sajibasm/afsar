<?php

namespace app\controllers;

use app\components\SalesApproveComponent;
use app\components\SalesDeleteComponent;
use app\components\SystemSettings;
use app\components\CommonUtility;

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\OutletUtility;
use app\components\PdfGen;
use app\components\ProductOutletUtility;
use app\components\ProductUtility;
use app\components\Utility;
use app\models\BankReconciliation;
use app\models\CashBook;
use app\models\Client;

use app\models\ClientPaymentDetails;
use app\models\ClientPaymentHistory;
use app\models\ClientTransactionSummary;
use app\models\DepositBook;

use app\models\PaymentType;
use app\models\ProductStatement;
use app\models\ProductStatementOutlet;
use app\models\SalesDetails;
use app\models\SalesDraft;
use app\models\SalesDraftSearch;

use app\models\SalesSMSQueue;
use app\models\Size;
use app\models\Transport;

use app\services\ClientFinancialService;
use kartik\form\ActiveForm;

use mdm\admin\components\Helper;
use Yii;
use app\models\Sales;
use app\models\SalesSearch;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $itemId = $parents[0];
                $brands = ProductUtility::getBrandListByItem($itemId);
                foreach ($brands as $brand) {
                    $out[] = ['id' => $brand->brand_id, 'name' => $brand->brand_name];
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetSizeListByBrand()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $itemId = $parents[0];
                $brandId = $parents[1];
                $sizes = ProductUtility::getSizeListByBrand($itemId, $brandId);
                foreach ($sizes as $size) {
                    $out[] = ['id' => $size->size_id, 'name' => $size->size_name];
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetProductPrice()
    {
        $out = [];

        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            if (isset($request['depdrop_parents'][0]) && $request['depdrop_parents'][0] != 0) {
                $sizeId = $request['depdrop_parents'][0];
                $stockPrice = ProductUtility::getProductStockPrice($sizeId);
                $price = $stockPrice;
                if ($price) {
                    $out[] = ['id' => $price->wholesale_price, 'name' => 'Wholesale: ' . $price->wholesale_price];
                    $out[] = ['id' => $price->retail_price, 'name' => 'Retail: ' . $price->retail_price];
                    if ($this->isCustomPriceEnable) {
                        $out[] = ['id' => 'custom', 'name' => 'Custom Price'];
                    }
                    return Json::encode(['output' => $out, 'selected' => '']);
                }
            }
        }

        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionCheckAvailableProduct()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            if (!empty($request['size_id']) && !empty($request['outletId'])) {
                $sizeId = $request['size_id'];
                $outletId = Utility::decrypt($request['outletId']);
                $response = $this->getAvailableQty($sizeId, $outletId);
            } else {
                $response = [
                    'error' => false,
                    'message' => "Size parameters invalid"
                ];
            }
        }
        return $response;
    }

    private function getAvailableQty($sizeId, $outletId)
    {

        $qty = ProductOutletUtility::getTotalQuantity($sizeId, $outletId) - ProductOutletUtility::getDraftProductQuantity($sizeId, $outletId);
        $stockPrice = ProductUtility::getProductStockPrice($sizeId);
        $sizeModel = Size::findOne($sizeId);
        $lowestPrice = 0;
        $costPrice = 0;

        if (isset($stockPrice->cost_price) && !empty($stockPrice->cost_price)) {
            $costPrice = $stockPrice->cost_price;
            $wholesale = $stockPrice->wholesale_price;
            $percent = $sizeModel->lowest_price;
            $lowestPrice = ($wholesale - (($wholesale / 100) * $percent));
        }

        if (doubleval($qty) > 0) {
            return [
                'isAvailable' => true,
                'costAmount' => $costPrice,
                'quantity' => doubleval($qty),
                'lowestPrice' => doubleval(floor($lowestPrice)),
                'message' => 'Quantity Available: ' . doubleval($qty) . ''
            ];
        }

        return [
            'error' => false,
            'costAmount' => $costPrice,
            'quantity' => doubleval($qty),
            'lowestPrice' => doubleval(floor($lowestPrice)),
            'message' => 'Quantity Available: ' . doubleval($qty) . ''
        ];
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

            // ✅ Check expiry
            if (time() > (int) $expiryTimestamp) {
                throw new BadRequestHttpException('The invoice link has expired.');
            }

            $model = Sales::findOne(['sales_id' => $salesId, 'client_id' => $clientId]);
            if (!$model) {
                throw new NotFoundHttpException('Invoice not found or access denied.');
            }

            $filename = Yii::getAlias('@runtime/') . "invoice_{$salesId}.pdf";
            PdfGen::salesInvoice($salesId, $filename);

            return Yii::$app->response->sendFile($filename, "Sales Invoice {$salesId}.pdf", [
                'mimeType' => 'application/pdf',
                'inline' => true,
            ])->on(\yii\web\Response::EVENT_AFTER_SEND, function () use ($filename) {
                @unlink($filename);
            });

        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid or expired access token.');
        }
    }

    public function actionPrint($id)
    {
        $invoice = Utility::decrypt($id);
        $filename = Yii::getAlias('@runtime/') ."invoice_{$invoice}.pdf";
        PdfGen::salesInvoice(Utility::decrypt($id), $filename);
        if (file_exists($filename)) {
            return Yii::$app->response->sendFile($filename, "Sales Invoice {$invoice}", [
                'mimeType' => 'application/pdf',
                'inline' => true,
            ])->on(Response::EVENT_AFTER_SEND, function ($event) use ($filename) {
                unlink($filename);
            });
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

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
                        ['in', 'reference_id', $reconciliationIds],
                        ['reference_table' => ClientFinancialService::REF_TABLE_RECONCILIATION]
                    ]
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

    public function actionIndex()
    {
        $searchModel = new SalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

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

            $salesId = Utility::decrypt($id);
            $model = $this->findModel($salesId);
            $model->setUserAction("Approved");
            $model->updated_by = Yii::$app->user->id;

            $component = new SalesApproveComponent();
            $result = $component->approve($model);

            $print = SystemSettings::invoiceAutoPrintWindow();
            $printLink = $print ? Url::base(true) . '/sales/print?id=' . Utility::encrypt($model->sales_id) : '';

            // Prepare common response payload
            $response = [
                'success' => $result['success'],
                'message' => $result['message'] ?? ($result['success'] ? 'Approval failed.' : 'Sales approved successfully.'),
                'data' => [
                    'salesId' => $model->sales_id,
                    'salesType' => $model->type
                ]
            ];

            // If approval succeeded → send notifications
            if ($result['success']) {
                if ($model->type == Sales::TYPE_SALES) {
                    if (SystemSettings::invoiceSMS()) {
//                    Yii::$app->queue->push(new SalesSMSQueue(['salesId' => $model->sales_id]));
                    }
                } elseif (SystemSettings::invoiceUpdateNotificationEmail() && !empty($model->client->email)) {
                    // Yii::$app->queue->push(new SalesUpdateEmailQueue(['salesId' => $model->sales_id]));
                }
            }

            return $response;
        }
    }

    public function actionNotification($id)
    {

        $model = $this->findModel(Utility::decrypt($id));
        $model->setUserAction("Notification Sent");

        if (Yii::$app->request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->post();

            if (isset($data['Sales']['email']) && !empty($data['Sales']['email'])) {
                if (EmailQueue::addQueue($model->sales_id, EmailQueue::TEMPLATE_INVOICE)) {
                    return ["error" => false, "message" => "successfully added"];
                } else {
                    return ["error" => true, "message" => "Error"];
                }
            }else{
                Yii::$app->queue->push(new SalesSMSQueue(['salesId'=>$model->sales_id]));
                return ["error" => false, "message" => "successfully added"];
            }
        }

    }

    public function actionTransport($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        if (Yii::$app->request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $data = Yii::$app->request->post('Sales');
            $transport = Transport::findOne($data['transport_id']);
            $model->setAttribute('transport_id', $data['transport_id']);
            $model->setAttribute('transport_name', $transport->transport_name);
            $model->setAttribute('tracking_number', $data['tracking_number']);
            $model->setUserAction("Transport Added");
            if ($model->save()) {

                if (SystemSettings::invoiceTrackingNotificationSMS()) {
                    Yii::$app->queue->push(new SalesSMSQueue(['salesId'=>$model->sales_id]));
                }

                if (SystemSettings::invoiceTrackingNotificationEmail()) {
                    //EmailQueue::addQueue($model->sales_id);
                    //TODO
                }

                return ["error" => false, "message" => "successfully added"];
            } else {
                return ["error" => true, "message" => ActiveForm::validate($model)];
            }
        }
    }

    public function actionCreate()
    {

        $storeIdEncrypted = Yii::$app->request->get('store');
        $storeId = $storeIdEncrypted ? Utility::decrypt($storeIdEncrypted) : null;
        if (empty($storeId) || !is_numeric($storeId)) {
            $model = new Sales();
            $model->setScenario('store');
            $userAssignedStores = OutletUtility::getUserOutlet();
            if (count($userAssignedStores) > 1) {
                if (Yii::$app->request->isPost) {
                    $model->load(Yii::$app->request->post());
                    if (!empty($model->outletId)) {
                        //Remove existing draft records
                        SalesDraft::deleteSalesHoldByUser(Yii::$app->user->getId());
                        return $this->redirect(['create', 'store' => Utility::encrypt($model->outletId)]);
                    }
                    $model->addError('outletId', 'Please select a outlet');
                }
            } else {
                //Remove existing draft records
                SalesDraft::deleteSalesHoldByUser(Yii::$app->user->getId());
                return $this->redirect(['create', 'store' => Utility::encrypt(array_key_first($userAssignedStores))]);
            }

            return $this->render('_store', [
                'model' => $model
            ]);
        }


        $store = $storeId;
        $model = new Sales();
        $model->outletId = $store;
        $model->setScenario('Sales');
        $model->user_id = Yii::$app->user->getId();
        $model->total_amount = SalesDraft::getTotal(null, SalesDraft::TYPE_INSERT, Yii::$app->user->getId());
        $model->received_amount = 0;
        $model->reconciliation_amount = 0;
        $model->sales_return_amount = 0;
        $model->paid_amount = 0;
        $model->due_amount = $model->total_amount;
        $model->discount_amount = 0;
        $model->payment_type = CommonUtility::getPaymentTypeId(PaymentType::TYPE_CASH);

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

            $data = Yii::$app->request->post();

            if (isset($data['SalesDraft'])) {

                $response = [];

                if (isset($data['SalesDraft']['size_id'])) {

                    $sizeId = (int)$data['SalesDraft']['size_id'];
                    $availableQty = $this->getAvailableQty($sizeId, $model->outletId);

                    $record = SalesDraft::find()->where([
                        'size_id' => $sizeId,
                        'user_id' => Yii::$app->user->getId(),
                        'type' => SalesDraft::TYPE_INSERT,
                        'outletId' => $model->outletId
                    ])->one();

                    if (isset($record->size_id) && !empty($record->size_id)) {
                        //finding added quantity from cart
                        $quantity = $record->quantity;
                        $record->load($data);
                        $record->outletId = $model->outletId;
                        $totalQty = $record->quantity + $quantity;

                        if ($totalQty > $availableQty['quantity']) {
                            $response = [
                                'error' => true,
                                'message' => 'Available Quantity: ' . $availableQty['quantity'] . ", Requested Quantity: " . $totalQty,
                                'type' => 'others'
                            ];
                        } else {

                            $record->sales_amount = $record->price;
                            $record->quantity = $totalQty;
                            $record->total_amount = $record->quantity * $record->sales_amount;
                            if (!$record->save()) {
                                $response = ['error' => true, 'message' => ActiveForm::validate($record), 'type' => 'model'];
                            }
                        }

                    } else {
                        $salesDraft = new SalesDraft();
                        $salesDraft->load($data);
                        $salesDraft->outletId = $store;
                        $salesDraft->user_id = Yii::$app->user->getId();
                        $salesDraft->type = SalesDraft::TYPE_INSERT;
                        $salesDraft->sales_amount = $salesDraft->price;
                        $salesDraft->total_amount = ($salesDraft->quantity * $salesDraft->sales_amount);
                        $sizeModel = Size::findOne($salesDraft->size_id);
                        $salesDraft->challan_unit = $sizeModel->productUnit->name;
                        $salesDraft->challan_quantity = $sizeModel->unit_quantity;
                        if ($salesDraft->quantity > $availableQty['quantity']) {
                            $response = [
                                'error' => true,
                                'message' => 'Available Quantity: ' . $availableQty['quantity'] . ", Requested Quantity: " . $salesDraft->quantity,
                                'type' => 'others'
                            ];
                        } else {
                            if (!$salesDraft->save()) {
                                $response = ['error' => true, 'message' => ActiveForm::validate($salesDraft), 'type' => 'model'];
                            }
                        }
                    }
                }

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;

            } else {
                $model->load(Yii::$app->request->post());
                $model->setUserAction("Sales Created");
                $model->received_amount = $model->paid_amount;
                $model->status = Sales::STATUS_PENDING;

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
                    if (empty($model->client_name)) {
                        $model->client_name = 'ABC';
                    }
                    if ($model->validate()) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($model->save()) {
                                if ($this->createInvoiceProductMovePermanent($model, SalesDetails::STATUS_PENDING, $store)) {
                                    $salesDraftResponse = SalesDraft::deleteAll(['type' => SalesDraft::TYPE_INSERT, 'user_id' => $model->user_id, 'outletid' => $store,]);
                                    if ($salesDraftResponse) {
                                        $transaction->commit();
                                        $message = 'Invoice #'.trim($model->sales_id).' has been created & need an action to approve.';
                                        if(Helper::checkRoute('approve')) {
                                            $message = 'Invoice #'.trim($model->sales_id).' has been created.';
                                        }

                                        FlashMessage::setMessage($message, 'Sales Created', 'success');
                                        return $this->redirect(['index']);
                                    }
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
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('new/create', [
                'model' => $model,
                'salesDraft' => $salesDraft,
                'salesDraftDataProvider' => $salesDraftDataProvider,
            ]);
        }

        return $this->render('new/create', [
            'model' => $model,
            'salesDraft' => $salesDraft,
            'salesDraftDataProvider' => $salesDraftDataProvider,
        ]);

    }

    private function createInvoiceProductMovePermanent($model, $status = SalesDetails::STATUS_PENDING, $outletId)
    {
        $salesDetailsRows = [];
        $productStatementRows = [];

        $salesAttr = ['sales_id', 'item_id', 'brand_id', 'size_id', 'cost_amount', 'sales_amount',
            'total_amount', 'quantity', 'unit', 'challan_unit', 'challan_quantity', 'outletId', 'status'];

        $productStatementOutletAttr = ['outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks',
            'reference_id', 'user_id', 'created_at', 'updated_at'
        ];


        $models = SalesDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'type' => SalesDraft::TYPE_INSERT, 'outletId' => $outletId])->all();

        foreach ($models as $product) {
            $salesDetailsRows[] = [
                $model->sales_id,
                $product->item_id,
                $product->brand_id,
                $product->size_id,
                $product->cost_amount,
                $product->sales_amount,
                $product->total_amount,
                $product->quantity,
                $product->challan_unit,
                $product->challan_unit,
                $product->challan_quantity,
                $product->outletId,
                $status
            ];

            $productStatementRows[] = [
                $outletId,
                $product->item_id,
                $product->brand_id,
                $product->size_id,
                -$product->quantity,
                ProductStatement::TYPE_SALES,
                'Sales - Pending',
                $model->sales_id,
                Yii::$app->user->getId(),
                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', Yii::$app->params['timeZone']),
                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', Yii::$app->params['timeZone'])
            ];
        }


        $totalSalesDetailsRows = count($salesDetailsRows);
        $totalSalesDetailsInsert = Yii::$app->db->createCommand()->batchInsert(SalesDetails::tableName(), $salesAttr, $salesDetailsRows)->execute();
        if ($totalSalesDetailsInsert == $totalSalesDetailsRows) {
            $productStatementInserted = Yii::$app->db->createCommand()->batchInsert(ProductStatementOutlet::tableName(), $productStatementOutletAttr, $productStatementRows)->execute();
            if ($productStatementInserted == count($productStatementRows)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function actionDraftUpdate($id)
    {
        $response = [];
        $model = SalesDraft::findOne(Utility::decrypt($id));
        $model->price = $model->sales_amount;
        $quantity = $model->quantity;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->total_amount = $model->price * $model->quantity;
            $model->sales_amount = $model->price;
            $availableQty = $this->getAvailableQty($model->size_id, $model->outletId);
            $totalQty = $availableQty['quantity'] + $quantity;
            if ($model->quantity > $totalQty) {
                $model->addError('quantity', "Stock does not have enough product , current stock is: " . $totalQty);
                $response = [
                    'error' => true,
                    'message' => "Stock does not have enough product , current stock is: " . $totalQty,
                    'type' => 'others'
                ];
            } else {
                if ($model->save()) {
                    $response = ['error' => false, 'message' => $model, 'type' => 'none'];
                } else {
                    $response = ['error' => true, 'message' => ActiveForm::validate($model), 'type' => 'model'];
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update/_draftupdate', ['model' => $model]);
        }
    }

    private function productMoveToDraft($salesId)
    {

        SalesDraft::deleteAll([
            'not', [
                'and',
                ['sales_id' => $salesId],
                ['user_id' => Yii::$app->user->id],
            ],
        ]);

        $records = SalesDraft::find()->where(['sales_id' => $salesId, 'user_id' => Yii::$app->user->id])->all();
        if(!$records){
            $salesDetailsRows = [];
            $products = SalesDetails::find()->where(['sales_id' => $salesId])->all();
            foreach ($products as $product) {
                $salesDetailsRows[] = [
                    'sales_id' => $product->sales_id,
                    'outletId' => $product->outletId,
                    'item_id' => $product->item_id,
                    'brand_id' => $product->brand_id,
                    'size_id' => $product->size_id,
                    'cost_amount' => $product->cost_amount,
                    'sales_amount' => $product->sales_amount,
                    'total_amount' => $product->total_amount,
                    'quantity' => $product->quantity,
                    'challan_unit' => $product->challan_unit,
                    'challan_quantity' => $product->challan_quantity,
                    'type' => SalesDraft::TYPE_UPDATE,
                    'user_id' => Yii::$app->user->getId(),
                ];
            }

            return Yii::$app->db->createCommand()->batchInsert(SalesDraft::tableName(), [
                'sales_id', 'outletId', 'item_id', 'brand_id', 'size_id', 'cost_amount', 'sales_amount',
                'total_amount', 'quantity', 'challan_unit', 'challan_quantity', 'type', 'user_id'
            ], $salesDetailsRows)->execute() ? true : false;
        }
    }

    public function actionInvoiceItemDelete($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = SalesDraft::findOne(Utility::decrypt($id));

        if ($model->type == SalesDraft::TYPE_UPDATE_ADDED || $model->type == SalesDraft::TYPE_INSERT) {
            if ($model->delete()) {
                return ["error" => false, "message" => "product removed permanently"];
            }
        } else {
            $salesDetails = SalesDetails::find()->where(['sales_id' => $model->sales_id, 'size_id' => $model->size_id])->one();
            $model->quantity = $salesDetails->quantity;
            $model->total_amount = ($model->sales_amount * $model->quantity);
            $model->price = $model->sales_amount;
            $model->type = SalesDraft::TYPE_UPDATE_DELETED;
            if ($model->save()) {
                return ["error" => false, "message" => "product removed", 'details' => $model];
            } else {
                return ["error" => true, "message" => ActiveForm::validate($model)];
            }
        }
    }

    public function actionCancelSalesInvoice()
    {
        SalesDraft::deleteAll(['user_id' => Yii::$app->user->getId(), 'type' => SalesDraft::TYPE_INSERT]);
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
            return (new SalesDeleteComponent())->delete($model);
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
