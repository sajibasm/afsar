<?php

namespace app\controllers;

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\OutletUtility;
use app\components\Utility;
use app\models\BankReconciliation;
use app\models\ClientPaymentHistory;
use app\models\CustomerAccount;
use app\models\PaymentType;
use app\models\ProductStatement;
use app\models\ReturnDraft;
use app\models\ReturnDraftSearch;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\SalesDetailsSearch;
use app\models\SalesDraft;
use app\models\SalesReturnDetails;
use mdm\admin\components\Helper;
use Yii;
use app\models\SalesReturn;
use app\models\SalesReturnSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * SalesReturnController implements the CRUD actions for SalesReturn model.
 */
class SalesReturnController extends Controller
{



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

    /**
     * @param \yii\base\Action $event
     * @return bool|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event){
        if(Yii::$app->asm->has()){
            return parent::beforeAction($event);
        }else{
            return Yii::$app->user->isGuest? $this->redirect(['/site/login']): $this->redirect(['/site/permission']);
        }
    }


    /**
     * Lists all SalesReturn models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalesReturnSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesReturn model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        if (Yii::$app->request->isAjax) {
            $model = $this->findModel(Utility::decrypt($id));
            if ($model->status == SalesReturn::STATUS_PENDING) {
                return $this->renderAjax('view', [
                    'model' => $model,
                ]);

            }
        } else {
            return $this->redirect(['index']);
        }
    }

    public function actionItemsRemove($id)
    {
        ReturnDraft::findOne(Utility::decrypt($id))->delete();
    }

    public function actionApproved($id)
    {
        $hasError = false;
        $hasMessage = '';
        $model = $this->findModel(Utility::decrypt($id));
        $model->setScenario('verify');
        $sales = Sales::find()->where(['sales_id' => $model->sales_id])->one();
        $salesReturnDetails = SalesReturnDetails::find()->where(['sales_return_id' => $model->sales_return_id])->all();

        $transaction = Yii::$app->db->beginTransaction();

        if ($model) {

            try {

                $productStatementRows = [];

                foreach ($salesReturnDetails as $product) {
                    $productStatementRows[] = [
                        'product_statement_id' => null,
                        'item_id' => $product->item_id,
                        'brand_id' => $product->brand_id,
                        'size_id' => $product->size_id,
                        'quantity' => $product->quantity,
                        'type' => ProductStatement::TYPE_SALES_RETURN,
                        'remarks' => $sales->remarks,
                        'reference_id' => $model->sales_return_id,
                        'user_id' => Yii::$app->user->getId(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')
                    ];
                }

                $productStatement = new ProductStatement();

                $productStatementInserted = Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), $productStatement->attributes(), $productStatementRows)->execute();

                if (count($productStatementRows) == $productStatementInserted) {

                    $accountCredit = 0;
                    $accountCreditBalance = 0;
                    $accountDebit = 0;
                    $accountDebitBalance = 0;

                    $totalInvoiceAmount = ($sales->total_amount - $sales->discount_amount);
                    $totalReceivedAmount =  ($sales->received_amount + $sales->sales_return_amount + $sales->reconciliation_amount);

                    if ($model->refund_amount > 0) {
                        $accountCredit = $model->refund_amount + $model->cut_off_amount;
                        $accountDebit = $model->refund_amount;
                        $accountCreditBalance = -1 * abs($model->refund_amount);
                    } else if ($model->cut_off_amount > 0) {
                        $accountCredit = $model->cut_off_amount;
                        $accountCreditBalance = (($totalInvoiceAmount - $totalReceivedAmount)-$accountCredit);
                    }


                    $customerAccountCredit = new CustomerAccount();
                    $customerAccountCredit->sales_id = $sales->sales_id;
                    $customerAccountCredit->memo_id = $sales->memo_id;

                    $customerAccountCredit->client_id = $sales->client_id;
                    $customerAccountCredit->type = CustomerAccount::TYPE_RETURN;
                    $customerAccountCredit->payment_type = CustomerAccount::PAYMENT_TYPE_NA;
                    $customerAccountCredit->account = CustomerAccount::ACCOUNT_SALES_RETURN;

                    $customerAccountCredit->debit = 0;
                    $customerAccountCredit->credit = $accountCredit;
                    $customerAccountCredit->balance = $accountCreditBalance;

                    if ($customerAccountCredit->save()) {

                        if ($model->refund_amount > 0) {
                            $customerAccountDebit = new CustomerAccount();
                            $customerAccountDebit->sales_id = $sales->sales_id;
                            $customerAccountDebit->memo_id = $sales->memo_id;

                            $customerAccountDebit->client_id = $sales->client_id;
                            $customerAccountDebit->type = CustomerAccount::TYPE_RETURN;
                            $customerAccountDebit->payment_type = CustomerAccount::PAYMENT_TYPE_NA;
                            $customerAccountDebit->account = CustomerAccount::ACCOUNT_ACCOUNT_DEPOSIT;

                            $customerAccountDebit->debit = $accountDebit;
                            $customerAccountDebit->credit = 0;
                            $customerAccountDebit->balance = $accountDebitBalance;
                            if ($customerAccountDebit->save()) {
                                $clientPaymentHistory = new ClientPaymentHistory();
                                $clientPaymentHistory->sales_id = $model->sales_id;
                                $clientPaymentHistory->client_id = $sales->client->client_id;
                                $clientPaymentHistory->user_id = Yii::$app->user->getId();
                                $clientPaymentHistory->received_type = ClientPaymentHistory::RECEIVED_TYPE_SALES_RETURN;
                                $clientPaymentHistory->received_amount = $model->refund_amount;
                                $clientPaymentHistory->remaining_amount = $model->refund_amount;
                                $clientPaymentHistory->remarks = $model->remarks;
                                $clientPaymentHistory->status = ClientPaymentHistory::STATUS_APPROVED;
                                $clientPaymentHistory->updated_by = $clientPaymentHistory->user_id;
                                $clientPaymentHistory->payment_type_id = PaymentType::TYPE_SALES_RETURN_ID;
                                if (!$clientPaymentHistory->save()) {
                                    $hasError = true;
                                    $hasMessage = ["Model"=>"$clientPaymentHistory", 'Errors'=>$clientPaymentHistory->getErrors()];
                                } else {
                                    $model->payment_history_id = $clientPaymentHistory->client_payment_history_id;
                                }
                            } else {
                                $hasMessage = ["Model"=>"$customerAccountDebit", 'Errors'=>$customerAccountDebit->getErrors()];
                                $hasError = true;
                            }
                        }

                        $sales->sales_return_amount += $accountCredit;
                        if ($sales->save()) {
                            $model->status = SalesReturn::STATUS_APPROVED;
                            $model->updated_by = Yii::$app->user->getId();
                            if (!$model->save()) {
                                $hasError = true;
                                $hasMessage = ["Model"=>"SalesReturn", 'Errors'=>$model->getErrors()];
                            }
                        } else {
                            $hasError = true;
                            $hasMessage = ["Model"=>"$sales", 'Errors'=>$sales->getErrors()];
                        }

                    } else {
                        $hasError = true;
                        Utility::debug($customerAccountCredit->getErrors());

                        $hasMessage = ["Model"=>"$sales", '$customerAccountCredit'=>$customerAccountCredit->getErrors()];
                    }
                }
            } catch (\Exception $e) {
                $hasError = true;
                throw $e;
                $hasMessage = ["Model"=>"Exception Block", 'Error'=>$e];
            }

            $response = [];

            if ($hasError) {
                $transaction->rollBack();
                $response =  ['status'=>'Has error found', 'Error' => true, "Details"=>$hasMessage];
            } else {
                $transaction->commit();
                $response = ['status' => 'Done', 'Error' => false];
            }

            if (Yii::$app->request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                $message = "Invoice# " . $model->sales_id . " Customer: " . $model->client_name . " and New Total Amount: " . $model->total_amount . " has been approved.";
                FlashMessage::setMessage($message, "Approved Invoice", "info");
                return $this->redirect(['index']);
            }

        }
    }

    public function actionItems($id)
    {
        $data = [];
        $model = SalesDetails::findOne(Utility::decrypt($id));
        if (isset($model->salesReturnDetails->quantity)) {
            $returnItems = SalesReturnDetails::find()->where(['sales_id' => $model->sales_id, 'size_id' => $model->size_id])->all();
            $total = 0;
            foreach ($returnItems as $items) {
                $total += $items->quantity;
            }
            $maxQuantity = ($model->quantity - $total);
            $model->quantity = $maxQuantity;
        } else {
            $maxQuantity = $model->quantity;
        }

        $model->item_name = $model->item->item_name;
        $model->brand_name = $model->brand->brand_name;
        $model->size_name = $model->size->size_name;
        $salesAmount = $model->sales_amount;

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());

            if ($maxQuantity < $model->quantity) {
                $data = ['error' => true, 'message' => 'Return quantity should be less then: ' . $maxQuantity];
            } else {
                $record = ReturnDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'size_id' => $model->size_id, 'sales_id' => $model->sales_id])->one();

                if (!$record) {
                    $returnDraft = new ReturnDraft();
                    $returnDraft->sales_id = $model->sales_id;
                    $returnDraft->item_id = $model->item_id;
                    $returnDraft->brand_id = $model->brand_id;
                    $returnDraft->size_id = $model->size_id;
                    $returnDraft->quantity = $model->quantity;
                    $returnDraft->refund_amount = $model->sales_amount;
                    $returnDraft->sales_amount = $salesAmount;
                    $returnDraft->total_amount = ($returnDraft->refund_amount * $model->quantity);
                    $returnDraft->user_id = Yii::$app->user->getId();
                    if ($returnDraft->save()) {
                        $data = ['error' => false, 'message' => 'success'];
                    }
                } else {
                    $data = ['error' => true, 'message' => 'This Product already added return Cart.'];
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        }

        return $this->renderAjax('_salesDetailsItems.php', [
            'model' => $model
        ]);

    }

    public function actionReturn($id)
    {
        $salesId = Utility::decrypt($id);

        $hasError = false;

        $model = new SalesReturn();
        $model->setScenario('verify');


        $account = CustomerAccount::find()->where(['sales_id' => $salesId])->orderBy('id DESC')->one();
        $account->paid_amount = ($account->sales->total_amount - $account->sales->discount_amount) - $account->balance;
        $account->due_amount = $account->balance;
        $account->discount_amount = $account->sales->discount_amount;
        $account->total_amount = $account->sales->total_amount;


        $salesModel = Sales::find()->where(['sales_id' => $salesId])->one();

        $salesTotalAmount = ($salesModel->total_amount - $salesModel->discount_amount);
        $salesTotalReceivedAmount = ($salesModel->received_amount + $salesModel->sales_return_amount + $salesModel->reconciliation_amount);

        if($salesTotalAmount>$salesTotalReceivedAmount){
            $dueAmount = $salesTotalAmount - $salesTotalReceivedAmount;
        }else{
            $dueAmount = 0;
        }


        $searchModel = new SalesDetailsSearch();
        $searchModel->sales_id = $salesId;
        $salesDataProvider = $searchModel->searchForReturn(Yii::$app->request->queryParams);


        $searchModel = new ReturnDraftSearch();
        $searchModel->sales_id = $salesId;
        $returnDataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $salesReturn = new SalesReturn();
        $salesReturn->user_id = Yii::$app->user->getId();
        $salesReturn->type = SalesReturn::TYPE_RETURN;

        $bankReconciliation = BankReconciliation::find()->where(['invoice_id' => $salesId])->one();
        if ($bankReconciliation) {
            $salesModel->reconciliationAmount = (int)($bankReconciliation->amount);
            $salesReturn->remarks = $bankReconciliation->reconciliation->name . " Reconciliation ID# " . $bankReconciliation->id;
        }

        $itemWiseTotalRefund = ReturnDraft::getTotal($salesId);
        //$salesReturn->cut_off_amount = ReturnDraft::getAdjustmentAmountBySalesId($salesId);

        //Utility::debug($totalRefund);

        if ($dueAmount == $itemWiseTotalRefund) {
            $salesReturn->cut_off_amount = $dueAmount;
            $salesReturn->refund_amount = 0;
            $salesReturn->total_amount = $salesReturn->cut_off_amount;
            $salesReturn->due_amount = $dueAmount;
        } else if ($dueAmount > $itemWiseTotalRefund) {
            $salesReturn->cut_off_amount = $itemWiseTotalRefund;
            $salesReturn->refund_amount = 0;
            $salesReturn->total_amount = $salesReturn->cut_off_amount;
            $salesReturn->due_amount = $dueAmount - $salesReturn->cut_off_amount;

            //$salesReturn->refund_amount = $itemWiseTotalRefund - (($salesModel->total_amount - $salesModel->discount_amount) - $salesModel->received_amount);
            //$salesReturn->total_amount = $salesReturn->refund_amount + $salesReturn->cut_off_amount;
        } else if ($dueAmount < $itemWiseTotalRefund) {
            $salesReturn->cut_off_amount = $dueAmount;
            $salesReturn->refund_amount = $itemWiseTotalRefund - $dueAmount;
            $salesReturn->total_amount = $salesReturn->cut_off_amount + $salesReturn->refund_amount;
            $salesReturn->due_amount = 0;
            //$salesReturn->cut_off_amount = $itemWiseTotalRefund - $dueAmount;
            //$salesReturn->refund_amount = $salesReturn->total_amount = $salesReturn->cut_off_amount;
        }


        $salesReturn->client_name = $salesModel->client->client_name;
        $salesReturn->client_id = $salesModel->client->client_id;
        $salesReturn->memo_id = $salesModel->memo_id;
        $salesReturn->client_mobile = $salesModel->client_mobile;
        $salesReturn->sales_id = $salesId;
        $salesReturn->soldDate = $salesModel->created_at;

        $products = ReturnDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'sales_id' => $salesReturn->sales_id])->all();

        if (Yii::$app->request->isPost) {

            $salesReturn->load(Yii::$app->request->post());
            $salesReturn->outletId = $salesReturn->sales->outletId;
            $salesReturn->status = SalesReturn::STATUS_PENDING;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($products) {
                    if ($salesReturn->save()) {
                        $salesReturnDetailsRows = [];
                        foreach ($products as $product) {
                            $salesDetailItems = SalesDetails::find()->where(['sales_id' => $salesId, 'size_id' => $product->size_id])->one();
                            $salesReturnDetailsRows[] = [
                                'sales_return_details_id' => null,
                                'sales_return_id' => $salesReturn->sales_return_id,
                                'sales_id' => $salesReturn->sales_id,
                                'item_id' => $product->item_id,
                                'brand_id' => $product->brand_id,
                                'size_id' => $product->size_id,
                                'refund_amount' => $product->refund_amount,
                                'sales_amount' => $salesDetailItems->sales_amount,
                                'total_amount' => $product->total_amount,
                                'quantity' => $product->quantity,

                            ];
                        }

                        $salesReturnDetails = new SalesReturnDetails();
                        $totalSalesDetailsInserted = Yii::$app->db->createCommand()->batchInsert(SalesReturnDetails::tableName(), $salesReturnDetails->attributes(), $salesReturnDetailsRows)->execute();
                        $totalRows = count($products);
                        if ($totalSalesDetailsInserted == $totalRows) {
                            $count = ReturnDraft::deleteAll("user_id = '" . Yii::$app->user->getId() . "' AND sales_id='" . $salesId . "'");
                            if ($count == $totalRows) {
                                $transaction->commit();
                            }

                            $message = "Invoice# " . $salesReturn->sales_id . " Customer: " . $salesReturn->client_name . " has been created sales return.";
                            FlashMessage::setMessage($message, "Update Invoice", "success");
                            //if(Helper::checkRoute('approved')){
                                return $this->redirect(['approved', 'id'=>Utility::encrypt($salesReturn->sales_return_id)]);
                            //}

                            return $this->redirect(['index']);

                        }
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $hasError = true;
                throw $e;
            }
        }


        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('return/index', [
                'model' => $salesModel,
                'account' => $account,
                'salesDataProvider' => $salesDataProvider,
                'salesReturn' => $salesReturn,
                'returnDataProvider' => $returnDataProvider,
            ]);
        } else {

            return $this->render('return/index', [
                'model' => $salesModel,
                'account' => $account,
                'salesDataProvider' => $salesDataProvider,
                'salesReturn' => $salesReturn,
                'returnDataProvider' => $returnDataProvider,
            ]);
        }
    }

    private function checkReturnableInvoice($salesId, $customerId)
    {
        $response = ['error' => false, 'message' => ''];
        $sales = Sales::find()->where(['sales_id' => $salesId, 'client_id' => $customerId])->one();
        if (!$sales) {
            $response = [
                'error' => true,
                'message' => 'Incorrect invoice and customer info.'
            ];
        } else {

            $salesReturn = SalesReturn::find()->where(['status'=>SalesReturn::STATUS_PENDING, 'sales_id'=>$salesId])->one();

            if(!$salesReturn){
                $today = DateTimeUtility::getDate(null, 'Y-m-d');
                $createDate = DateTimeUtility::getDate($sales->created_at, 'Y-m-d');
                if ($today == $createDate) {
//                $response = [
//                    'error'=>true,
//                    'message'=>"Invoice # {$salesId} can't returnable for today. Return will be active at next day, you can only update only."
//                ];
                }
            }else{
                $response = [
                    'error'=>true,
                    'message'=>"Already exists one sales return for invoice # {$salesId}, Please approved this sales return (ID # ".$salesReturn->sales_return_id.") then you can create."
                ];
            }


        }

        return $response;
    }

    public function actionVerify()
    {
        $model = new SalesReturn();
        $model->setScenario('verify');
        if(OutletUtility::numberOfOutletByUser()===1){
            $model->outletId = OutletUtility::defaultOutletByUser();
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $response = $this->checkReturnableInvoice($model->sales_id, $model->client_id);
            if ($response['error']) {
                $model->addError('sales_return_id', $response['message']);
            } else {
                $this->redirect(['return', 'id' => Utility::encrypt($model->sales_id)]);
            }
        }

        return $this->render('customer', [
            'model' => $model,
        ]);
    }

    public function actionVerifyRepair()
    {
        $model = new SalesReturn();
        $model->setScenario('verify');

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $sales = Sales::find()->where(['sales_id' => $model->sales_id, 'client_id' => $model->client_id])->one();
            if (!$sales) {
                $model->addError('sales_return_id', 'Invoice #' . $model->sales_id . ' not found for this customer');
            } else {
                $this->redirect(['service', 'id' => Utility::encrypt($model->sales_id)]);
            }
        }

        return $this->render('customer', [
            'model' => $model,
        ]);
    }

    public function actionService($id)
    {
        $salesId = Utility::decrypt($id);

        $model = new SalesReturn();
        $model->setScenario('verify');

        $balance = 0;

        $searchModel = new SalesDetailsSearch();
        $searchModel->sales_id = $salesId;
        $salesDataProvider = $searchModel->searchForReturn(Yii::$app->request->queryParams);

        $account = CustomerAccount::find()->where(['sales_id' => $salesId])->orderBy('id DESC')->one();
        $account->paid_amount = ($account->sales->total_amount - $account->sales->discount_amount) - $account->balance;
        $account->due_amount = $account->balance;
        $account->discount_amount = $account->sales->discount_amount;
        $account->total_amount = $account->sales->total_amount;

        $balance = $account->balance;

        $searchModel = new ReturnDraftSearch();
        $searchModel->sales_id = $salesId;
        $returnDataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $salesReturn = new SalesReturn();
        $salesReturn->user_id = Yii::$app->user->getId();
        $salesReturn->type = SalesReturn::TYPE_RETURN;
        $salesReturn->due_amount = $account->balance;


        $refundTotal = ReturnDraft::getTotal($salesId);

        if ($refundTotal > 0) {
            $salesReturn->refund_amount = ($refundTotal - $salesReturn->due_amount) - $account->sales->discount_amount;
            $salesReturn->total_amount = $salesReturn->refund_amount;
        }

        $salesReturn->cut_off_amount = 0;
        $salesReturn->client_name = $account->sales->client_name;
        $salesReturn->client_id = $account->sales->client_id;
        $salesReturn->memo_id = $account->sales->memo_id;
        $salesReturn->client_mobile = $account->sales->client_mobile;
        $salesReturn->sales_id = $account->sales->sales_id;
        $salesReturn->soldDate = $account->sales->created_at;

        $salesReturn->maxRefundAmount = $account->sales->total_amount - abs($account->balance);

        $products = ReturnDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'sales_id' => $salesReturn->sales_id])->all();

        if (Yii::$app->request->isPost) {

            $salesReturn->load(Yii::$app->request->post());
            $salesReturn->type = SalesReturn::TYPE_REPAIR;
            $salesReturn->outletId = $salesReturn->sales->outletId;
            $salesReturn->total_amount += $salesReturn->cut_off_amount;

            if ($salesReturn->total_amount <= 0) {
                $salesReturn->addError('refund_amount', 'Please enter some amount');
            } else {

                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();

                try {

                    if ($salesReturn->save()) {

                        $customerAccount = new CustomerAccount();
                        $customerAccount->sales_id = $account->sales_id;
                        $customerAccount->memo_id = $account->memo_id;
                        $customerAccount->client_id = $account->client_id;
                        $customerAccount->type = CustomerAccount::TYPE_REPAIR;
                        $customerAccount->payment_type = CustomerAccount::PAYMENT_TYPE_NA;
                        $customerAccount->account = CustomerAccount::ACCOUNT_SALES_REPAIR;
                        $customerAccount->debit = 0;
                        $customerAccount->credit = $salesReturn->total_amount;
                        $customerAccount->balance = $account->balance - $customerAccount->credit;

                        if ($customerAccount->save()) {
                            $transaction->commit();
                            return $this->redirect(['/sales-return/index']);
                        } else {
                            $transaction->rollBack();
                        }

                    } else {
                        $transaction->rollBack();
                    }

                } catch (\Exception $e) {

                    $transaction->rollBack();
                    throw $e;
                }


            }
        }


        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('service/service', [
                'model' => $model,
                'account' => $account,
                'salesDataProvider' => $salesDataProvider,
                'salesReturn' => $salesReturn,
            ]);

        } else {
            return $this->render('service/service', [
                'model' => $model,
                'account' => $account,
                'salesDataProvider' => $salesDataProvider,
                'salesReturn' => $salesReturn,
            ]);
        }

    }

    /**
     * Creates a new SalesReturn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesReturn();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SalesReturn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

 

    /**
     * Finds the SalesReturn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesReturn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesReturn::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
