<?php

namespace app\controllers;

use app\components\SystemSettings;
use app\components\CommonUtility;

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\OutletUtility;
use app\components\PdfGen;
use app\components\ProductOutletUtility;
use app\components\ProductUtility;
use app\components\TransactionApproved;
use app\components\TransactionRestore;
use app\components\TransactionStore;
use app\components\Utility;
use app\models\BankReconciliation;
use app\models\CashBook;
use app\models\Client;

use app\models\ClientPaymentDetails;
use app\models\ClientPaymentHistory;
use app\models\ClientSalesPayment;
use app\models\CustomerAccount;
use app\models\DepositBook;
use app\models\EmailQueue;

use app\models\PaymentType;
use app\models\ProductStatement;
use app\models\ProductStatementOutlet;
use app\models\SalesDetails;
use app\models\SalesDraft;
use app\models\SalesDraftSearch;

use app\models\SalesSMSQueue;
use app\models\Size;
use app\models\Transport;

use app\modules\asm\components\ASM;
use kartik\form\ActiveForm;

use Yii;
use app\models\Sales;
use app\models\SalesSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
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
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
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

    public function actionOutlet()
    {
        $model = new Sales();
        $model->setScenario('outlet');
        $outlets = OutletUtility::getUserOutlet();
        if (count($outlets) > 1) {
            if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                if (!empty($model->outletId)) {
                    return $this->redirect(['create', 'outlet' => Utility::encrypt($model->outletId)]);
                }
                $model->addError('outletId', 'Please select a outlet');
            }
        } else {
            return $this->redirect(['create', 'outlet' => Utility::encrypt(array_key_first($outlets))]);
        }

        return $this->render('_outlet', [
            'model' => $model
        ]);
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

    public function actionPrint($id)
    {
        $this->view->title = 'Hello';
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->charset ='utf-8';
        //Yii::$app->controller->view->title = 'Print Invoice';
        $fileContent = PdfGen::salesInvoice(Utility::decrypt($id), false);
        return $fileContent;
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

    public function actionApproved($id)
    {
        $print = false;
        $printLink = '';
        if (!empty($id)) {

            $hasError = false;
            $approveType = null;
            $model = $this->findModel(Utility::decrypt($id));
            $model->updated_by = Yii::$app->user->id;
            if ($model->type == Sales::TYPE_SALES_UPDATE) {
                $approveType = $model->type;
                $type = SalesDraft::TYPE_UPDATE_PENDING;
            } else {
                $type = SalesDraft::TYPE_SALES_PENDING;
            }

            $response = [];
            $saveRecords = $this->saveSales($model, $model->sales_id, $type);

            if (!$saveRecords['error']) {

                if ($approveType == Sales::TYPE_SALES_UPDATE) {
                    TransactionApproved::sales($model->sales_id);
                }

                if ($model->type == Sales::TYPE_SALES) {
                    if (SystemSettings::invoiceEmail()) {
                        if (!empty($model->client->email)) {
                            //EmailQueue::addQueue($model->sales_id);
                        }
                    }

                    if (SystemSettings::invoiceSMS()) {
                        Yii::$app->queue->push(new SalesSMSQueue(['salesId'=>$model->sales_id]));
                    }

                } else {
                    if (SystemSettings::invoiceUpdateNotificationEmail()) {
                        if (!empty($model->client->email)) {
                            EmailQueue::addQueue($model->sales_id);
                        }
                    }
                }

                if (SystemSettings::invoiceAutoPrintWindow()) {
                    $print = true;
                    $printLink = Url::base(true) . '/sales/print?id=' . Utility::encrypt($model->sales_id);
                }
                $response = ['status' => 'Done', 'Error' => false, "details" => $saveRecords['message'], 'print' => $print, 'printLink' => $printLink];
            } else {
                $response = ['status' => 'Has error found', 'Error' => true, "details" => $saveRecords['message'], 'print' => $print, 'printLink' => $printLink];
            }

            if (Yii::$app->request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            } else {

                $message = "Invoice# " . $model->sales_id . " Customer: " . $model->client_name . " and New Total Amount: " . $model->total_amount . " has been approved.";
                FlashMessage::setMessage($message, "Approved Invoice", "info");

                $session = Yii::$app->session;
                $session['salesInvoiceAutoPrint'] = $model->sales_id;

                return $this->redirect(['index']);
            }

        } else {
            return $this->redirect(['index']);
        }
    }

    private function saveSales(Sales $model, $salesId, $type)
    {
        $hasError = false;
        $hasMessage = '';

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {

            if ($model->type == Sales::TYPE_SALES_UPDATE) {
                $paymentHistoryDetails = ClientPaymentDetails::find()->where(['sales_id' => $salesId])->one();
                if ($paymentHistoryDetails) {
                    if ($model->paid_amount > $paymentHistoryDetails->paid_amount) {
                        $model->paid_amount = $model->received_amount = ($model->paid_amount - $paymentHistoryDetails->paid_amount);
                        $model->due_amount = ($model->total_amount - $model->paid_amount);
                    }

                    $paymentHistory = ClientPaymentHistory::findOne($paymentHistoryDetails->payment_history_id);
                    $paymentHistory->remaining_amount += $paymentHistoryDetails->paid_amount;
                    if ($paymentHistory->save()) {
                        if (!$paymentHistoryDetails->delete()) {
                            $hasError = true;
                        }
                    } else {
                        $hasError = true;
                    }
                }
            }

            $model->status = Sales::STATUS_APPROVED;
            $model->type = Sales::TYPE_SALES;

            if ($model->paymentTypeModel->type == PaymentType::TYPE_CASH) {
                $model->bank = null;
                $model->branch = null;
            }

            $sql = null;

            if ($model->save()) {

                $paymentType = PaymentType::find()->where(['payment_type_id' => $model->payment_type])->one();

                $sql = "UPDATE sales_details SET status='" . SalesDetails::STATUS_APPROVED . "' WHERE sales_id='" . $model->sales_id . "'";
                $salesDetailsResponse = Yii::$app->db->createCommand($sql)->execute();

                if ($salesDetailsResponse) {

                    $sql = "UPDATE product_statement_outlet SET remarks=:remarks WHERE reference_id=:refId";
                    $productStatementResponse = Yii::$app->db->createCommand($sql)->bindValues([':remarks' => 'Approved', ':refId' => $model->sales_id])->execute();
                    if ($productStatementResponse) {
                        $customerAccount = new CustomerAccount();
                        $customerAccount->sales_id = $model->sales_id;
                        $customerAccount->memo_id = $model->memo_id;
                        $customerAccount->client_id = $model->client_id;
                        $customerAccount->type = CustomerAccount::TYPE_SALES;
                        $customerAccount->payment_history_id = null;
                        $customerAccount->account = CustomerAccount::ACCOUNT_RECEIVABLE;
                        $customerAccount->debit = ($model->total_amount - $model->discount_amount);
                        $customerAccount->credit = 0;
                        $customerAccount->balance = $customerAccount->debit;
                        $customerAccount->payment_type = CustomerAccount::PAYMENT_TYPE_NA;

                        if ($customerAccount->save()) {

                            if ($model->paid_amount > 0) {

                                unset($customerAccount->id);
                                $customerAccount->setIsNewRecord(true);

                                $customerAccount = new CustomerAccount();
                                $customerAccount->sales_id = $model->sales_id;
                                $customerAccount->memo_id = $model->memo_id;
                                $customerAccount->client_id = $model->client_id;
                                $customerAccount->type = CustomerAccount::TYPE_SALES;
                                $customerAccount->account = CustomerAccount::ACCOUNT_SALES;
                                $customerAccount->payment_history_id = null;
                                $customerAccount->account = CustomerAccount::ACCOUNT_SALES;
                                $customerAccount->debit = 0;
                                $customerAccount->credit = $model->paid_amount;
                                $customerAccount->balance = ($model->total_amount - $model->discount_amount) - $model->paid_amount;
                                $customerAccount->payment_type = $paymentType->type == PaymentType::TYPE_CASH ? CustomerAccount::PAYMENT_TYPE_CASH : CustomerAccount::PAYMENT_TYPE_BANK;

                                if ($customerAccount->save()) {

                                    if ($paymentType->type == PaymentType::TYPE_CASH) {
                                        $cashBook = new CashBook();
                                        $cashBook->outletId = $model->outletId;
                                        $cashBook->cash_in = $model->paid_amount;
                                        $cashBook->cash_out = 0;
                                        $cashBook->source = CashBook::SOURCE_SALES;
                                        $cashBook->reference_id = $model->sales_id;
                                        $cashBook->ref_user_id = Yii::$app->user->getId();
                                        $cashBook->remarks = $model->remarks;
                                        if ($cashBook->save()) {
                                            $transaction->commit();
                                        } else {
                                            $hasError = true;
                                            $hasMessage = "Total Added product and current cart don't not match 1";
                                            $transaction->rollBack();
                                        }
                                    } else {
                                        $depositBook = new DepositBook();
                                        $depositBook->outletId = $model->outletId;
                                        $depositBook->bank_id = $model->bank;
                                        $depositBook->branch_id = $model->branch;
                                        $depositBook->payment_type_id = $model->payment_type;
                                        $depositBook->payment_type_id = $model->payment_type;
                                        $depositBook->ref_user_id = $model->user_id;
                                        $depositBook->deposit_in = $model->paid_amount;
                                        $depositBook->deposit_out = 0;
                                        $depositBook->reference_id = $model->sales_id;
                                        $depositBook->source = DepositBook::SOURCE_SALES;
                                        $depositBook->remarks = $model->remarks;
                                        if ($depositBook->save()) {
                                            $transaction->commit();
                                        } else {
                                            $hasError = true;
                                            $hasMessage = $depositBook->getErrors();
                                            $transaction->rollBack();
                                        }
                                    }

                                } else {
                                    $hasError = true;
                                    $hasMessage = $customerAccount->getErrors();
                                    $transaction->rollBack();
                                }
                            } else {
                                $transaction->commit();
                            }
                        } else {
                            $hasError = true;
                            $hasMessage = $customerAccount->getErrors();
                            $transaction->rollBack();
                        }

                    } else {
                        $hasError = true;
                        $hasMessage = "unable to update product remarks";
                        $transaction->rollBack();
                    }
                } else {
                    $hasError = true;
                    $hasMessage = "unable to update sales details remarks ";
                    $transaction->rollBack();
                }
            } else {
                $hasError = true;
                $hasMessage = $model->getErrors();
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $hasError = true;
            $hasMessage = $e;
            $transaction->rollBack();
            throw $e;
        }

        return ['error' => $hasError, 'message' => $hasMessage];
    }

    public function actionNotification($id)
    {

        $model = $this->findModel(Utility::decrypt($id));

        if (Yii::$app->request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->post();

            if (isset($data['Sales']['email']) && !empty($data['Sales']['email'])) {
//                if (EmailQueue::addQueue($model->sales_id, EmailQueue::TEMPLATE_INVOICE)) {
//                    return ["error" => false, "message" => "successfully added"];
//                } else {
//                    return ["error" => true, "message" => "Error"];
//                }
            }else{
                Yii::$app->queue->push(new SalesSMSQueue(['salesId'=>$model->sales_id]));
                return ["error" => false, "message" => "successfully added"];
            }
        }

        return $this->renderAjax('notification', [
            'model' => $model,
        ]);

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

        return $this->renderAjax('transport', [
            'model' => $model,
        ]);

    }

    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel(Utility::decrypt($id));
            if ($model->status == Sales::STATUS_PENDING) {
                return $this->renderAjax('view', [
                    'model' => $model,
                ]);

            }
        } else {
            return $this->redirect(['index']);
        }
    }

    public function actionCreate($outlet)
    {
        $outlet = Utility::decrypt($outlet);
        $model = new Sales();
        $model->outletId = $outlet;
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
        $salesDraft->outletId = $outlet;
        $salesDraft->type = SalesDraft::TYPE_INSERT;

        $salesDraftSearchModel = new SalesDraftSearch();
        $salesDraftSearchModel->type = SalesDraft::TYPE_INSERT;
        $salesDraftSearchModel->outletId = $outlet;
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
                        $salesDraft->outletId = $outlet;
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

                $addedRules = false;
                $model->load(Yii::$app->request->post());
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
                                if ($this->createInvoiceProductMovePermanent($model, SalesDetails::STATUS_PENDING, $outlet)) {
                                    $salesDraftResponse = SalesDraft::deleteAll(['type' => SalesDraft::TYPE_INSERT, 'user_id' => $model->user_id, 'outletid' => $outlet,]);
                                    if ($salesDraftResponse) {
                                        $transaction->commit();
                                        $message = "Invoice# " . $model->sales_id . " Customer: " . $model->client_name . " and Total Amount: " . $model->total_amount . " has been created. Please Approved This";
                                        FlashMessage::setMessage($message, "New Invoice", "success");
                                        if (!Yii::$app->user->can('sales/approved')) {
                                            return $this->redirect(['approved', 'id' => Utility::encrypt($model->sales_id)]);
                                        }
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
            return $this->renderPartial('create', [
                'model' => $model,
                'salesDraft' => $salesDraft,
                'salesDraftDataProvider' => $salesDraftDataProvider,
            ]);
        }


        return $this->render('create', [
            'model' => $model,
            'salesDraft' => $salesDraft,
            'salesDraftDataProvider' => $salesDraftDataProvider,
        ]);

    }

    public function actionUpdate($id)
    {

        $id = Utility::decrypt($id);
        $userId = Yii::$app->user->getId();
        $model = $this->findModel($id);
        $status = $model->status;

        if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') != DateTimeUtility::getDate(null, 'd-m-Y')) {
            throw new ForbiddenHttpException('Insufficient privileges to update this invoice');
        }

        $this->productMoveToDraft($id);
        $previousPaidAmount = $model->paid_amount;
        $salesDraft = new SalesDraft();
        $salesDraft->user_id = Yii::$app->user->getId();
        $salesDraft->sales_id = $id;

        $salesDraftSearchModel = new SalesDraftSearch();
        $salesDraftSearchModel->sales_id = $id;
        $salesDraftSearchModel->user_id = $salesDraft->user_id;
        $salesDraftSearchModel->outletId = $model->outletId;
        $salesDraftDataProvider = $salesDraftSearchModel->searchUpdate(Yii::$app->request->queryParams);

        $salesDraftRemoveSearchModel = new SalesDraftSearch();
        $salesDraftRemoveSearchModel->sales_id = $id;
        $salesDraftRemoveSearchModel->user_id = $salesDraft->user_id;
        $salesDraftRemoveSearchModel->outletId = $model->outletId;
        $salesDraftRemoveDataProvider = $salesDraftRemoveSearchModel->searchUpdateRemoved(Yii::$app->request->queryParams);


        if (Yii::$app->request->isPost) {

            $hasMessage = '';
            $hasError = false;
            $data = Yii::$app->request->post();

            //Product add or remove functionality.
            if (isset($data['SalesDraft'])) {

                \Yii::$app->response->format = Response::FORMAT_JSON;

                if (isset($data['SalesDraft']['size_id'])) {
                    $record = SalesDraft::find()->where(['size_id' => $data['SalesDraft']['size_id'], 'sales_id' => $id, 'user_id' => $userId])->one();
                    if ($record) {
                        if ($record->type === SalesDraft::TYPE_UPDATE) {
                            return ["error" => true, "message" => ["Existing Invoice of Goods can update Or delete is permitted."]];
                        } elseif ($record->type == SalesDraft::TYPE_UPDATE_DELETED) {
                            $newSalesDraft = new SalesDraft();
                            $newSalesDraft->load($data);
                            $newSalesDraft->sales_id = $model->sales_id;
                            $newSalesDraft->user_id = $userId;
                            $newSalesDraft->type = SalesDraft::TYPE_UPDATE_ADDED;
                            $newSalesDraft->sales_amount = $newSalesDraft->price;
                            $newSalesDraft->total_amount = $newSalesDraft->quantity * $newSalesDraft->sales_amount;
                            if ($newSalesDraft->save()) {
                                \Yii::$app->response->format = Response::FORMAT_JSON;
                                return ["error" => false, "message" => "new product added successfully"];
                            } else {
                                return ["error" => true, "message" => $newSalesDraft->getErrors()];
                            }
                        }


                        //else {
//                            $record->load($data);
//                            $record->total_amount = $record->quantity * $record->sales_amount;
//                            if ($record->save()) {
//                                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//                                return ["error" => false, "message" => "updated product successfully"];
//                            } else {
//                                return ["error" => true, "message" => $record->getErrors()];
//                            }
                        //}
                    } else {
                        $newSalesDraft = new SalesDraft();
                        $newSalesDraft->sales_id = $model->sales_id;
                        $newSalesDraft->outletId = $model->outletId;
                        $newSalesDraft->user_id = $userId;
                        $newSalesDraft->load($data);
                        $newSalesDraft->type = SalesDraft::TYPE_UPDATE_ADDED;
                        $newSalesDraft->sales_amount = $newSalesDraft->price;
                        $newSalesDraft->total_amount = $newSalesDraft->quantity * $newSalesDraft->sales_amount;
                        if ($newSalesDraft->save()) {
                            return ["error" => false, "message" => "new product added successfully"];
                        } else {
                            return ["error" => true, "message" => $newSalesDraft->getErrors()];
                        }
                    }
                }
            } else {

                $oldPaymentType = $model->paymentTypeModel->type;
                $model->load($data);
                $model->status = Sales::STATUS_PENDING;
                $model->type = Sales::TYPE_SALES_UPDATE;
                $model->due_amount = ($model->total_amount - $model->discount_amount) - $model->paid_amount;
                $model->received_amount = $model->paid_amount;

                $paymentTypeModel = PaymentType::findOne($model->payment_type);

                if ($model->paid_amount > $model->total_amount) {
                    $model->addError('paid_amount', 'should be less or equal to total amount');
                } else if ($paymentTypeModel->type == PaymentType::TYPE_DEPOSIT && (empty($model->bank) || empty($model->branch))) {
                    $model->bank = 0;
                    $model->branch = 0;
                    $model->payment_type = 0;
                    $model->addError('bank', 'Bank Can\'t be Empty');
                    $model->addError('branch', 'Branch Can\'t be Empty');
                } else {

                    $transaction = Yii::$app->db->beginTransaction();

                    TransactionStore::sales($model->sales_id);

                    try {

                        if ($model->save()) {
                            if ($this->stockRestore($model)) {

                                if ($status == Sales::STATUS_APPROVED) {
                                    $customerAccountCount = CustomerAccount::deleteAll(['sales_id' => $model->sales_id]);
                                    if ($previousPaidAmount > 0) {
                                        if ($oldPaymentType == PaymentType::TYPE_CASH) {
                                            $count = CashBook::deleteAll(['reference_id' => $model->sales_id]);
                                            if ($count == 0) {
                                                $hasError = true;
                                                $hasMessage = "Unable to process cash book";
                                            }
                                        } else {
                                            $count = DepositBook::deleteAll(['reference_id' => $model->sales_id]);
                                            if ($count == 0) {
                                                $hasError = true;
                                                $hasMessage = "Unable to process deposit book";
                                            }
                                        }
                                    }
                                }

                                $countSalesDetails = SalesDraft::deleteAll(['sales_id' => $model->sales_id, 'user_id' => $userId]);
                                if ($countSalesDetails == 0) {
                                    $hasError = true;
                                    $hasMessage = "Unable to process sales draft (remove sales details)";
                                }

                            } else {
                                $hasError = true;
                                $hasMessage = "Restore Error";
                            }

                        } else {
                            $hasError = true;
                            $hasMessage = "Unable to process sales model";
                        }

                        if ($hasError) {
                            $model->bank = 0;
                            $model->branch = 0;
                            $model->payment_type = 0;
                            $transaction->rollBack();
                            $model->addError('client_name', $hasMessage);
                        } else {
                            $transaction->commit();
                            $message = "Invoice# " . $model->sales_id . " Customer: " . $model->client_name . " and New Total Amount: " . $model->total_amount . " has been updated.";
                            FlashMessage::setMessage($message, "Update Invoice", "success");
                            if (Yii::$app->asm->can('approved')) {
                                return $this->redirect(['approved', 'id' => Utility::encrypt($model->sales_id)]);
                            }

                            return $this->redirect(['index']);
                        }

                    } catch (\Exception $e) {
                        $model->bank = 0;
                        $model->branch = 0;
                        $model->payment_type = 0;
                        $transaction->rollBack();
                        $model->addError('client_name', $e);
                    }
                }
            }
        }

        $updateAmount = SalesDraft::getUpdateTotal($model->sales_id);

        $model->total_amount = $updateAmount;

        if ($model->total_amount == 0) {
            $model->paid_amount = $model->due_amount = 0;
        } else {
            $model->due_amount = ($model->total_amount - $model->discount_amount) - $model->paid_amount;
        }

        if ($model->paid_amount > $model->total_amount) {
            $model->addError('paid_amount', 'should be less or equal to total amount');
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('update', [
                'model' => $model,
                'salesDraft' => $salesDraft,
                'salesDraftDataProvider' => $salesDraftDataProvider,
                'salesDraftRemoveDataProvider' => $salesDraftRemoveDataProvider,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'salesDraft' => $salesDraft,
            'salesDraftDataProvider' => $salesDraftDataProvider,
            'salesDraftRemoveDataProvider' => $salesDraftRemoveDataProvider,
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

        $record = SalesDraft::find()
            ->where(['and', 'sales_id=' . $salesId, ['or',
                "type='" . SalesDraft::TYPE_UPDATE . "'",
                "type='" . SalesDraft::TYPE_UPDATE_ADDED . "'",
                "type='" . SalesDraft::TYPE_UPDATE_DELETED . "'",
                "type='" . SalesDraft::TYPE_UPDATE_PENDING . "'"
                //]])->createCommand()->getRawSql();
            ]])->all();

        if (!$record) {
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

            Yii::$app->db->createCommand()->batchInsert(SalesDraft::tableName(), [
                'sales_id', 'outletId', 'item_id', 'brand_id', 'size_id', 'cost_amount', 'sales_amount',
                'total_amount', 'quantity', 'challan_unit', 'challan_quantity', 'type', 'user_id'
            ], $salesDetailsRows)->execute() ? true : false;

        } else {
            FlashMessage::setMessage("Some one already working/open on this invoice.", "Sales Update", "error");
        }

        return false;
    }

    private function stockRestore(Sales $model)
    {
        $hasError = false;

        $salesDrafts = SalesDraft::find()->where(['sales_id' => $model->sales_id])->all();

        foreach ($salesDrafts as $salesDraft) {


            if ($salesDraft->type == SalesDraft::TYPE_UPDATE) {

                $salesDetails = SalesDetails::find()->where([
                    'sales_id' => $model->sales_id,
                    'item_id' => $salesDraft->item_id,
                    'brand_id' => $salesDraft->brand_id,
                    'size_id' => $salesDraft->size_id
                ])->one();

                $salesDetails->quantity = $salesDraft->quantity;
                $salesDetails->status = SalesDetails::STATUS_PENDING;
                if ($salesDetails->save()) {
                    $productStatementModel = ProductStatementOutlet::find()->where([
                        'reference_id' => $model->sales_id,
                        'outlet_id' => $model->outletId,
                        'item_id' => $salesDraft->item_id,
                        'brand_id' => $salesDraft->brand_id,
                        'size_id' => $salesDraft->size_id
                    ])->one();

                    $productStatementModel->quantity = -$salesDraft->quantity;
                    $productStatementModel->type = ProductStatement::TYPE_SALES_UPDATE;
                    $productStatementModel->remarks = $model->remarks . ' Sales Update';
                    if (!$productStatementModel->save()) {
                        $hasError = true;
                    }
                }
            } elseif ($salesDraft->type == SalesDraft::TYPE_UPDATE_ADDED) {

                $size = Size::findOne($salesDraft->size_id);
                $salesDetailsModel = new SalesDetails();
                $salesDetailsModel->sales_id = $model->sales_id;
                $salesDetailsModel->item_id = $salesDraft->item_id;
                $salesDetailsModel->outletId = $model->outletId;
                $salesDetailsModel->brand_id = $salesDraft->brand_id;
                $salesDetailsModel->size_id = $salesDraft->size_id;
                $salesDetailsModel->cost_amount = $salesDraft->cost_amount;
                $salesDetailsModel->sales_amount = $salesDraft->sales_amount;
                $salesDetailsModel->total_amount = $salesDraft->total_amount;
                $salesDetailsModel->quantity = $salesDraft->quantity;
                $salesDetailsModel->challan_unit = $salesDraft->challan_unit;
                $salesDetailsModel->challan_quantity = $salesDraft->challan_quantity;
                $salesDetailsModel->status = SalesDetails::STATUS_PENDING;
                if ($salesDetailsModel->save()) {
                    $productStatementModel = new ProductStatementOutlet();
                    $productStatementModel->item_id = $salesDraft->item_id;
                    $productStatementModel->brand_id = $salesDraft->brand_id;
                    $productStatementModel->size_id = $salesDraft->size_id;
                    $productStatementModel->outlet_id = $model->outletId;
                    $productStatementModel->quantity = -$salesDraft->quantity;
                    $productStatementModel->type = ProductStatement::TYPE_SALES_UPDATE;
                    $productStatementModel->remarks = $model->remarks . 'New Product Added';
                    $productStatementModel->reference_id = $model->sales_id;
                    $productStatementModel->user_id = Yii::$app->user->getId();
                    if (!$productStatementModel->save()) {
                        $hasError = true;
                    }
                } else {
                    $hasError = true;
                }
            } elseif ($salesDraft->type == SalesDraft::TYPE_UPDATE_DELETED) {
                $salesDetails = SalesDetails::find()->where([
                    'sales_id' => $model->sales_id,
                    'item_id' => $salesDraft->item_id,
                    'brand_id' => $salesDraft->brand_id,
                    'size_id' => $salesDraft->size_id
                ])->one();

                if ($salesDetails->delete()) {
                    $productStatementModel = new ProductStatementOutlet();
                    $productStatementModel->item_id = $salesDraft->item_id;
                    $productStatementModel->brand_id = $salesDraft->brand_id;
                    $productStatementModel->size_id = $salesDraft->size_id;
                    $productStatementModel->outlet_id = $model->outletId;
                    $productStatementModel->quantity = $salesDraft->quantity;
                    $productStatementModel->type = ProductStatement::TYPE_SALES_UPDATE;
                    $productStatementModel->remarks = $model->remarks . 'Product (Restore)';
                    $productStatementModel->reference_id = $model->sales_id;
                    $productStatementModel->user_id = Yii::$app->user->getId();
                    if (!$productStatementModel->save()) {
                        $hasError = true;
                    }
                }
            } else {
                $salesDraft->status = SalesDetails::STATUS_PENDING;
                $salesDraft->save();
            }
        }

        return $hasError ? false : true;

    }

    public function actionInvoiceItemUpdateRestore($id)
    {
        $model = SalesDraft::findOne(Utility::decrypt($id));
        $model->price = $model->sales_amount;
        $model->type = SalesDraft::TYPE_UPDATE;
        if ($model->save()) {
            return true;
        }
    }

    public function actionInvoiceItemUpdateDelete($id)
    {
        $model = SalesDraft::findOne(Utility::decrypt($id));

        if ($model->type == SalesDraft::TYPE_UPDATE_ADDED) {
            $model->delete();
        } else {
            $model->price = $model->sales_amount;
            $model->type = SalesDraft::TYPE_UPDATE_DELETED;
            if (!$model->save()) {
            } else {
                return true;
            }
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

    public function actionCancelUpdateInvoice($id)
    {
        SalesDraft::deleteAll(['sales_id' => Utility::decrypt($id)]);
        $this->redirect('index');
    }

    public function actionDeleteInvoice($id)
    {
        //if (Yii::$app->request->isAjax) {
        //if(!Helper::checkRoute('approved')) {
        //  return "You are not allowed to perform this action.";
        //}
        $model = $this->findModel(Utility::decrypt($id));
        if ($model->status == Sales::STATUS_APPROVED) {
            return $this->renderAjax('remove-invoice', [
                'model' => $model,
            ]);
        }
        //}
    }

    /**
     * Deletes an existing Sales model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemoveInvoice($id)
    {
        $hasError = false;
        $message = '';
        $productStatementRows = [];

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel(Utility::decrypt($id));
        $salesDetails = SalesDetails::find()->where(['sales_id' => $model->sales_id])->all();
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $cashBook = CashBook::find()->where(['reference_id' => $model->sales_id, 'source' => CashBook::SOURCE_SALES])->one();
            if ($cashBook && !$cashBook->delete()) {
                $hasError = true;
                $message = "Unable to remove cashbook";
            }

            $depositBook = DepositBook::find()->where(['reference_id' => $model->sales_id, 'source' => DepositBook::SOURCE_SALES])->one();

            if ($depositBook && !$depositBook->delete()) {
                $hasError = true;
                $message = "Unable to remove depositBook";
            }


            if ($hasError == false && CustomerAccount::deleteAll(['sales_id' => $model->sales_id])) {

                $model->paid_amount = 0;
                $model->due_amount = 0;
                $model->discount_amount = 0;
                $model->received_amount = 0;
                $model->total_amount = 0;
                $model->reconciliation_amount = 0;
                $model->sales_return_amount = 0;
                $model->status = Sales::STATUS_DELETE;

                if ($model->save()) {

                    $bankReconciliation = BankReconciliation::find()->where(['invoice_id' => $model->sales_id])->one();
                    if ($bankReconciliation) {
                        $bankReconciliation->amount = 0;
                        $bankReconciliation->remarks = "Delete Invoice";
                        $bankReconciliation->status = BankReconciliation::STATUS_DELETE;
                        if (!$bankReconciliation->save()) {
                            $hasError = true;
                            $message = "unable to update BankReconciliation";
                        }
                    }

                    foreach ($salesDetails as $details) {
                        $productStatementRows[] = [
                            'item_id' => $details->item_id,
                            'brand_id' => $details->brand_id,
                            'size_id' => $details->size_id,
                            'outlet_id' => $model->outletId,
                            'quantity' => $details->quantity,
                            'type' => ProductStatementOutlet::TYPE_SALES_DELETE,
                            'remarks' => $model->remarks,
                            'reference_id' => $model->sales_id,
                            'user_id' => Yii::$app->user->getId(),
                            'created_at' => DateTimeUtility::getDate(null, 'Y-m-d H:i:s'),
                            'updated_at' => DateTimeUtility::getDate(null, 'Y-m-d H:i:s')
                        ];

                        $details->status = SalesDetails::STATUS_DELETE;
                        if (!$details->save()) {
                            $hasError = true;
                        }
                    }

                    $rows = Yii::$app->db->createCommand()->batchInsert(ProductStatementOutlet::tableName(), [
                        'item_id', 'brand_id', 'size_id', 'outlet_id', 'quantity', 'type',
                        'remarks', 'reference_id', 'user_id', 'created_at', 'updated_at'
                    ], $productStatementRows)->execute();
                    if ($rows == count($productStatementRows)) {

                        $clientPaymentDetails = ClientPaymentDetails::find()->where(['sales_id' => $model->sales_id])->one();
                        if ($clientPaymentDetails) {
                            $clientPaymentHistory = ClientPaymentHistory::findOne($clientPaymentDetails->payment_history_id);
                            $clientPaymentHistory->remaining_amount += $clientPaymentDetails->paid_amount;
                            if ($clientPaymentHistory->save()) {
                                if (!$clientPaymentDetails->delete()) {
                                    $hasError = true;
                                    $message = "unable to delete clientPaymentDetails";
                                }
                            } else {
                                $message = "unable to update clientPaymentHistory";
                            }
                        }

                    } else {
                        $hasError = true;
                        $message = "doesn\'t match list of product and product statement list {$rows}";
                    }

                } else {
                    $hasError = true;
                    $message = "Unable to update Sales Table";
                }


                if ($hasError == false) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }

            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return [
            'error' => $hasError,
            'message' => $message,
        ];

    }

    public function actionRestore($id)
    {
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $response = TransactionRestore::sales(Utility::decrypt($id));
            $message = "Invoice# " . Utility::decrypt($id) . " has been restore.";
            FlashMessage::setMessage($message, "Approved Invoice", "success");
            return ['status' => 'Done', 'Error' => $response, "details" => "", 'print' => "", 'printLink' => ""];
        } else {
            return $this->redirect(['index']);
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
