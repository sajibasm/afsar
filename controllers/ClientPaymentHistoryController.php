<?php

namespace app\controllers;


use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\OutletUtility;
use app\components\PdfGen;
use app\components\SystemSettings;
use app\components\Utility;
use app\models\CashBook;

use app\models\ClientPaymentDetails;
use app\models\CustomerAccount;
use app\models\CustomerAccountSearch;
use app\models\CustomerPaymentQueue;
use app\models\CustomerWithdraw;
use app\models\DepositBook;
use app\models\EmailQueue;
use app\models\PaymentType;
use app\models\Sales;
use app\models\Serialize;
use app\modules\admin\components\Helper;

use Yii;
use app\models\ClientPaymentHistory;
use app\models\ClientPaymentHistorySearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ClientPaymentHistoryController implements the CRUD actions for ClientPaymentHistory model.
 */
class ClientPaymentHistoryController extends Controller
{

    public $invoice = [];

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
     * @return bool|Response
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
     * Lists all ClientPaymentHistory models.
     * @return mixed
     */
    public function actionIndex()
    {


        $searchModel = new ClientPaymentHistorySearch();
        if(OutletUtility::numberOfOutletByUser()===1){
            $searchModel->outletId = OutletUtility::defaultOutletByUser();
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionNotification($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        if (Yii::$app->request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->post();

            if (isset($data['Payment']['email']) && !empty($data['Payment']['email'])) {
//                if (EmailQueue::addQueue($model->client_payment_history_id, EmailQueue::TEMPLATE_PAYMENT_RECEIPT)) {
//                    return ["error" => false, "message" => "successfully added"];
//                } else {
//                    return ["error" => true, "message" => "Error"];
//                }
            }else{
                Yii::$app->queue->push(new CustomerPaymentQueue(['paymentId'=>$model->client_payment_history_id]));
                return ["error" => false, "message" => "Success"];
            }
        }

        return $this->renderAjax('notification', [
            'model' => $model,
        ]);

    }

    public function actionPrint($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->controller->view->title = 'Test';
        return PdfGen::paymentReceipt(Utility::decrypt($id), false);
    }

    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel(Utility::decrypt($id));
            //$model->client_payment_history_id = $id;
            if ($model->status == ClientPaymentHistory::STATUS_PENDING) {
                return $this->renderAjax('view', [
                    'model' => $model,
                ]);

            }
        } else {
            return $this->redirect(['index']);
        }
    }

    public function actionApproved($id)
    {

        $response = [];

        $errorMessage = "";
        $model = $this->findModel(Utility::decrypt($id));
        $model->status = ClientPaymentHistory::STATUS_APPROVED;
        $model->updated_by = Yii::$app->user->getId();
        $totalDues = CustomerUtility::getTotalDuesByCustomer($model->client_id);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {

            $hasError = false;
            $advanceAmount = $model->received_amount - $totalDues;
            if ($totalDues < $model->received_amount) {

                if ($totalDues == 0) {
                    $model->received_type = ClientPaymentHistory::RECEIVED_TYPE_ADVANCED;
                } else {
                    $model->received_amount = $totalDues;
                    $model->remaining_amount = $model->received_amount;
                    $advancePayment = new ClientPaymentHistory();
                    $advancePayment->outletId = $model->outletId;
                    $advancePayment->client_id = $model->client_id;
                    $advancePayment->user_id = $model->user_id;
                    $advancePayment->updated_by = $model->updated_by;
                    $advancePayment->received_amount = $advanceAmount;
                    $advancePayment->remaining_amount = $advanceAmount;
                    $advancePayment->remarks = $model->remarks . ' Split From Transaction Id#(' . $model->client_payment_history_id . ")";
                    $advancePayment->received_type = ClientPaymentHistory::RECEIVED_TYPE_ADVANCED;
                    $advancePayment->extra = $model->extra;
                    $advancePayment->payment_type_id = $model->payment_type_id;
                    $advancePayment->status = ClientPaymentHistory::STATUS_APPROVED;
                    if ($advancePayment->save()) {
                        $model->remarks = $model->remarks . ' New Split Transaction Id# (' . $advancePayment->client_payment_history_id . ")";
                    } else {
                        $errorMessage = $advancePayment->getErrors();
                        $hasError = true;
                    }
                }
            }


            if ($model->save()) {

                if ($model->paymentType->type == PaymentType::TYPE_CASH) {
                    $cashBook = new CashBook();
                    $cashBook->outletId = $model->outletId;
                    $cashBook->cash_in = $model->received_amount;
                    $cashBook->cash_out = 0;
                    $cashBook->source = CashBook::SOURCE_DUE_RECEIVED;
                    $cashBook->reference_id = $model->client_payment_history_id;
                    $cashBook->remarks = $model->remarks;
                    if ($totalDues == 0) {
                        $cashBook->source = CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED;
                    }

                    if ($cashBook->save()) {
                        if ($advanceAmount > 0 && $totalDues > 0) {
                            $newCashBook = new CashBook();
                            $newCashBook->outletId = $model->outletId;
                            $newCashBook->cash_in = $advanceAmount;
                            $newCashBook->cash_out = 0;
                            $newCashBook->source = CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED;
                            $newCashBook->remarks = $model->remarks;
                            if (!empty($advancePayment)) {
                                $newCashBook->reference_id = $advancePayment->client_payment_history_id;
                            }
                            if (!$newCashBook->save()) {
                                $errorMessage = $newCashBook->getErrors();
                                $hasError = true;
                            }
                        }
                    } else {
                        $errorMessage = $cashBook->getErrors();
                        $hasError = true;
                    }
                } elseif ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                    $json = (object)Json::decode($model->extra);
                    $depositBook = new DepositBook();
                    $depositBook->outletId = $model->outletId;
                    $depositBook->ref_user_id = $model->client_id;
                    $depositBook->payment_type_id = $model->payment_type_id;
                    $depositBook->bank_id = $json->bank_id;
                    $depositBook->branch_id = $json->branch_id;
                    $depositBook->deposit_in = $model->received_amount;
                    $depositBook->deposit_out = 0;
                    $depositBook->source = DepositBook::SOURCE_DUE_RECEIVED;
                    $depositBook->reference_id = $model->client_payment_history_id;
                    $depositBook->remarks = $model->remarks;

                    if ($totalDues == 0) {
                        $depositBook->source = CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED;
                    }

                    if ($depositBook->save()) {
                        if ($advanceAmount > 0 && $totalDues > 0) {
                            $newDepositBook = new DepositBook();
                            $newDepositBook->outletId = $model->outletId;
                            $newDepositBook->ref_user_id = $model->client_id;
                            $newDepositBook->payment_type_id = $model->payment_type_id;
                            $newDepositBook->bank_id = $json->bank_id;
                            $newDepositBook->branch_id = $json->branch_id;
                            $newDepositBook->deposit_in = $advanceAmount;
                            $newDepositBook->deposit_out = 0;
                            $newDepositBook->source = DepositBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED;
                            $newDepositBook->remarks = $model->remarks;
                            if (!empty($advancePayment)) {
                                $newDepositBook->reference_id = $advancePayment->client_payment_history_id;
                            }

                            if (!$newDepositBook->save()) {
                                $errorMessage = $newDepositBook->getErrors();
                                $hasError = true;
                            }
                        }
                    } else {
                        $errorMessage = $depositBook->getErrors();
                        $hasError = true;
                    }
                }
            } else {
                $errorMessage = $model->getErrors();
                $hasError = true;
            }

            if (!$hasError) {
                $transaction->commit();
                if(SystemSettings::customerDueReceivedSMS()){
                    Yii::$app->queue->push(new CustomerPaymentQueue(['paymentId'=>$model->client_payment_history_id]));
                }
                $response = ['status' => 'Done', 'Error' => false];
            } else {
                $transaction->rollBack();
                $response = ['status' => 'Has error found', 'Error' => true, "Details" => $errorMessage];
            }

        } catch (\Exception $e) {
            Utility::debug($e);
            $transaction->rollBack();
            $response = ['status' => 'Has error found', 'Error' => true, "Details" => $e];
        }


        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        } else {
            $message = "Customer Payment #" . $model->received_amount . " Customer: " . $model->customer->client_name . " has been approved.";
            FlashMessage::setMessage($message, "Approved Invoice", "info");
            return $this->redirect(['index']);
        }


    }

    private function processWithdraw(ClientPaymentHistory $model)
    {
        $extra = ['paymentType' => PaymentType::TYPE_CASH, 'bank' => $model->bank_id, 'branch' => $model->branch_id];
        $customerWithDraw = new CustomerWithdraw();
        $customerWithDraw->payment_history_id = $model->client_payment_history_id;
        $customerWithDraw->client_id = $model->client_id;
        $customerWithDraw->amount = $model->remaining_amount;
        $customerWithDraw->remarks = $model->remarks;
        $customerWithDraw->type = $model->paymentType->type;
        $customerWithDraw->extra = Json::encode($extra);
        $customerWithDraw->created_by = Yii::$app->user->getId();
        $customerWithDraw->status = CustomerWithdraw::STATUS_PENDING;
        if ($customerWithDraw->save()) {
            $model->status = ClientPaymentHistory::STATUS_DECLINED;
            $model->customerWithdrawId = $customerWithDraw->id;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

    public function actionWithdraw($id)
    {


        $model = $this->findModel(Utility::decrypt($id));
        $model->remarks = '';
        $model->payment_type_id = 0;
        $model->name = $model->customer->client_name;
        $model->setScenario('withdrawMode');
        $addRules = false;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                if (empty($model->bank_id) || empty($model->branch_id)) {
                    $addRules = true;
                    $model->payment_type_id = 0;
                    $model->bank_id = 0;
                    $model->branch_id = 0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                }
            }

            if ($addRules == false) {

                if ($model->validate()) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($this->processWithdraw($model)) {
                            $transaction->commit();
                            $message = "Customer: " . $model->customer->client_name . " and Amount: " . $model->received_amount . " has been requested for withdraw.";
                            FlashMessage::setMessage($message, "Withdraw", "success");
                            return $this->redirect(['/customer-withdraw/approved', 'id' => Utility::encrypt($model->customerWithdrawId)]);
                        } else {
                            $transaction->rollBack();
                            $message = "Customer: " . $model->customer->client_name . " and Amount: " . $model->received_amount . " has been rejected to withdraw.";
                            FlashMessage::setMessage($message, "Withdraw", "warning");
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        $message = "Customer: " . $model->customer->client_name . " and Amount: " . $model->received_amount . " has been rejected to withdraw(Exception).";
                        FlashMessage::setMessage($message, "Withdraw", "info");
                        throw  $e;
                    }
                } else {
                    Utility::debug($model->getErrors());
                }

            }
        }

        return $this->render('withdraw/withdraw', [
            'model' => $model,
        ]);

    }

    private function processPayment(ClientPaymentHistory $model)
    {
        $hasError = false;
        $availableBalance = $model->remaining_amount;


        if ($model->payType == 'Manual') {
            $receivable = CustomerUtility::getDueInvoiceById($model->client_id, $model->invoices);
        } else {
            $receivable = CustomerUtility::getDueInvoicePrice($model->client_id);
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {

            foreach ($receivable as $account) {

                if ($availableBalance > 0) {

                    $adjustableAmount = 0;

                    if ($availableBalance>=$account->due) {
                        //This Invoice due and available balance is same amount.
                        $adjustableAmount = $account->due;
                    } else {
                        //available balance and due amount is same.
                        $adjustableAmount = $availableBalance;
                    }

                    $sales = Sales::find()->where(['sales_id' => $account->sales_id])->one();
                    $clientPaymentDetails = new ClientPaymentDetails();
                    $clientPaymentDetails->sales_id = $account->sales_id;
                    $clientPaymentDetails->client_id = $model->client_id;
                    $clientPaymentDetails->payment_history_id = $model->client_payment_history_id;
                    $clientPaymentDetails->paid_amount = $adjustableAmount;
                    $clientPaymentDetails->payment_type = ClientPaymentDetails::PAYMENT_TYPE_FULL;
                    if ($clientPaymentDetails->save()) {
                        $record = CustomerAccount::find()->where(['sales_id' => $account->sales_id])->orderBy('id DESC')->one();
                        $customerAccount = new CustomerAccount();
                        $customerAccount->sales_id = $account->sales_id;
                        $customerAccount->memo_id = $sales->memo_id."-".rand(1,999);
                        $customerAccount->client_id = $model->client_id;
                        $customerAccount->payment_history_id = $model->client_payment_history_id;
                        $customerAccount->type = CustomerAccount::TYPE_SALES;
                        $customerAccount->payment_type = $model->paymentType->type == PaymentType::TYPE_CASH ? CustomerAccount::PAYMENT_TYPE_CASH : CustomerAccount::PAYMENT_TYPE_BANK;
                        $customerAccount->account = CustomerAccount::ACCOUNT_DUE_RECEIVED;
                        $customerAccount->debit = 0;
                        $customerAccount->credit = $adjustableAmount;
                        $customerAccount->balance = ($record->balance - $adjustableAmount);
                        if (!$customerAccount->save()) {
                            $hasError = true;
                        }
                    } else {
                        $hasError = true;
                    }

                    $availableBalance -=$adjustableAmount;

                    if (DateTimeUtility::getDate($model->received_at) == DateTimeUtility::getDate($sales->created_at)) {
                        $sales->paid_amount += $adjustableAmount;
                        $sales->received_amount += $adjustableAmount;
                        $sales->due_amount = ( $sales->total_amount - $sales->paid_amount );
                        if (!$sales->save()) {
                            $hasError = true;
                        }
                    }else{
                        $sales->received_amount += $adjustableAmount;
                        if (!$sales->save()){
                            $hasError = true;
                        }
                    }
                }else{
                    break;
                }
            }

            $model->remaining_amount = $availableBalance;
            if(!$hasError){
                if ($model->save()) {
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    return false;
                }
            }else{
                $transaction->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

    }

    public function actionPay($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->name = $model->customer->client_name;
        $model->setScenario('payMode');

        if ($model->remaining_amount == 0) {
            throw new HttpException(404, 'The requested payment id # ' . $model->client_payment_history_id . ' already paid.');
        }

        $searchModel = new CustomerAccountSearch();
        $searchModel->client_id = $model->client_id;
        $dataProvider = $searchModel->searchHasBalance(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($this->processPayment($model)) {
                $this->redirect(['index']);
            }

        }

        return $this->render('pay/pay', [
            'model' => $model,
            'dataProvider' => $dataProvider,

        ]);

    }

    /**
     * Creates a new ClientPaymentHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClientPaymentHistory();
        $model->setScenario('add');
        $model->user_id = Yii::$app->user->getId();
        $model->extra = Json::encode(['bank_id' => 0, 'branch_id' => 0]);
        $model->status = ClientPaymentHistory::STATUS_PENDING;
        $addRules = false;
        if(OutletUtility::numberOfOutletByUser()===1){
            $model->outletId = OutletUtility::defaultOutletByUser();
        }


        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());
            $model->remaining_amount = $model->received_amount;

            if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                if (empty($model->bank_id) || empty($model->branch_id)) {
                    $addRules = true;
                    $model->payment_type_id = 0;
                    $model->bank_id = 0;
                    $model->branch_id = 0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                } else {
                    $model->extra = Json::encode(['bank_id' => $model->bank_id, 'branch_id' => $model->branch_id]);
                }
            } else {
                $model->extra = Json::encode(['bank_id' => null, 'branch_id' => null]);
            }

            if ($addRules == false) {
                if ($model->save()) {
                    $totalDues = CustomerUtility::getTotalDuesByCustomer($model->client_id);
                    $advanceAmount = $model->received_amount - $totalDues;
                    if ($totalDues < $model->received_amount) {
                        $message = "Transaction # " . $model->client_payment_history_id . " Customer " . $model->customer->client_name . " and Total Amount: " . $model->received_amount . " has been added. If approved this, will received as to payment (Due Received: " . $totalDues . ") and (Advance Received: " . $advanceAmount . ")";
                    } else {
                        $message = "Transaction # " . $model->client_payment_history_id . " Customer " . $model->customer->client_name . " and Total Amount: " . $model->received_amount . " has been added. It's Required to approved.";
                    }

                    FlashMessage::setMessage($message, "Payment Received", "success");

                    if (Yii::$app->asm->can('approved')) {
                        return $this->redirect(['approved', 'id' => Utility::encrypt($model->client_payment_history_id)]);
                    }

                    $this->redirect(['index']);
                }
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing ClientPaymentHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    private function restoreInvoiceAmount(ClientPaymentHistory $PaymentHistoryModel)
    {

        $clientPaymentDetails = ClientPaymentDetails::find()
            ->where(['payment_history_id'=>$PaymentHistoryModel->client_payment_history_id])
            ->all();

        foreach ($clientPaymentDetails as $clientPaymentDetail){

            $adjustableAmount = $clientPaymentDetail->paid_amount;

            $sales = Sales::findOne($clientPaymentDetail->sales_id);

            if (DateTimeUtility::getDate($PaymentHistoryModel->received_at) == DateTimeUtility::getDate($sales->created_at)) {
                $sales->paid_amount -= $adjustableAmount;
                $sales->received_amount -= $adjustableAmount;
                $sales->due_amount = ( $sales->total_amount - $sales->paid_amount );
                if (!$sales->save()) {
                    return false;
                }
            }else{
                $sales->received_amount -= $adjustableAmount;
                if (!$sales->save()){
                    return false;
                }
            }

            $record = CustomerAccount::find()->where(['sales_id'=>$sales->sales_id])->orderBy('id DESC')->one();

            $customerAccount = new CustomerAccount();
            $customerAccount->sales_id = $sales->sales_id;
            $customerAccount->memo_id = $sales->memo_id;
            $customerAccount->client_id = $sales->client_id;
            $customerAccount->payment_history_id = $PaymentHistoryModel->client_payment_history_id;
            $customerAccount->type = CustomerAccount::TYPE_SALES;
            $customerAccount->payment_type = CustomerAccount::PAYMENT_TYPE_NA;
            $customerAccount->account = CustomerAccount::ACCOUNT_DUE_RECEIVED_RESTORE;
            $customerAccount->debit = $adjustableAmount;
            $customerAccount->credit = 0;
            $customerAccount->balance = ($record->balance+$adjustableAmount);
            if (!$customerAccount->save()) {
                return false;
            }


            if(!$clientPaymentDetail->delete()){
                return false;
            }
        }

        return true;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->setScenario('add');
        $model->user_id = Yii::$app->user->getId();
        //$model->status = ClientPaymentHistory::STATUS_PENDING;
        $extra = Json::decode($model->extra);
        $model->bank_id = $extra['bank_id'];
        $model->branch_id = $extra['branch_id'];
        $model->source = $model->received_type;
        $addRules = false;
        $oldPaymentType = $model->paymentType->type;


        if (Yii::$app->request->isPost) {

            $serialize = new Serialize();
            $serialize->source = ClientPaymentHistory::tableName();
            $serialize->refId = $model->client_payment_history_id;
            $serialize->data = Utility::serializeModel($model, false);
            $serialize->created_by = Yii::$app->user->getId();

            $model->load(Yii::$app->request->post());
            $model->remaining_amount = $model->received_amount;
            $paymentType = PaymentType::findOne($model->payment_type_id);

            //For Validation Purpose.
            if ($paymentType->type == PaymentType::TYPE_DEPOSIT) {
                if (empty($model->bank_id) || empty($model->branch_id)) {
                    $addRules = true;
                    $model->payment_type_id = 0;
                    $model->bank_id = 0;
                    $model->branch_id = 0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                } else {
                    $model->extra = Json::encode(['bank_id' => $model->bank_id, 'branch_id' => $model->branch_id]);
                }
            } else {
                $model->extra = Json::encode(['bank_id' => null, 'branch_id' => null]);
            }

            if ($addRules == false) {

                $hasError = false;
                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();

                try {
                    if ($model->save()) {
                        if ($serialize->save()) {
                            if ($oldPaymentType == PaymentType::TYPE_DEPOSIT) {
                                $depositBook = DepositBook::find()
                                    ->andWhere(['reference_id' => $model->client_payment_history_id, 'source' => CashBook::SOURCE_DUE_RECEIVED])
                                    ->orWhere(['reference_id' => $model->client_payment_history_id, 'source' => CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED])
                                    ->one();

                                $serialize2 = new Serialize();
                                $serialize2->source = ClientPaymentHistory::tableName() . CashBook::tableName();
                                $serialize2->refId = $model->client_payment_history_id;
                                $serialize2->data = Utility::serializeModel($depositBook, false);
                                $serialize2->created_by = Yii::$app->user->getId();
                                if ($serialize2->save()) {
                                    if (!$depositBook->delete()) {
                                        $hasError = true;
                                    }
                                } else {
                                    $hasError = true;
                                }
                            } else {
                                $cashBook = CashBook::find()
                                    ->andWhere(['reference_id' => $model->client_payment_history_id, 'source' => CashBook::SOURCE_DUE_RECEIVED])
                                    ->orWhere(['reference_id' => $model->client_payment_history_id, 'source' => CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED])
                                    ->one();

                                $serialize2 = new Serialize();
                                $serialize2->source = ClientPaymentHistory::tableName() . CashBook::tableName();
                                $serialize2->refId = $model->client_payment_history_id;
                                $serialize2->data = Utility::serializeModel($cashBook, false);
                                $serialize2->created_by = Yii::$app->user->getId();
                                if ($serialize2->save()) {
                                    if (!$cashBook->delete()) {
                                        $hasError = true;
                                    }
                                } else {
                                    $hasError = true;
                                }
                            }

                            if ($hasError) {
                                $transaction->rollBack();
                            } else {
                                if($this->restoreInvoiceAmount($model)){
                                    $transaction->commit();
                                }else{
                                    $transaction->rollBack();
                                }
                            }


                            $totalDues = CustomerUtility::getTotalDuesByCustomer($model->client_id);
                            $advanceAmount = $model->received_amount - $totalDues;
                            if ($totalDues < $model->received_amount) {
                                $message = "Transaction # " . $model->client_payment_history_id . " Customer " . $model->customer->client_name . " and Total Amount: " . $model->received_amount . " has been added. If approved this, will received as to payment (Due Received: " . $totalDues . ") and (Advance Received: " . $advanceAmount . ")";
                            } else {
                                $message = "Transaction # " . $model->client_payment_history_id . " Customer " . $model->customer->client_name . " and Total Amount: " . $model->received_amount . " has been added. It's Required to approved.";
                            }

                            FlashMessage::setMessage($message, "Payment Received", "success");
                            if (Yii::$app->asm->can('approved')) {
                                return $this->redirect(['approved', 'id' => Utility::encrypt($model->client_payment_history_id)]);
                            }

                            $this->redirect(['index']);

                        } else {
                            $transaction->rollBack();
                            Utility::debug($serialize->getErrors());
                        }
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }

            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ClientPaymentHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClientPaymentHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientPaymentHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
