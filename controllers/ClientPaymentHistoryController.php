<?php

namespace app\controllers;


use app\components\ClientPaymentApprovalService;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\PaymentSettlementService;
use app\components\StoreUtility;
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
use app\models\SalesSearch;
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
                    'delete' => ['POST'],
                    'approve' => ['POST']
                ]
            ]
        ];
    }

    /**
     * Lists all ClientPaymentHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientPaymentHistorySearch();
        if(StoreUtility::countUserStores()===1){
            $searchModel->outletId = StoreUtility::getDefaultStoreByUser();
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


    public function actionApprove()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Payment ID is required.'];
        }

        $clientPaymentApprovalService = new ClientPaymentApprovalService();

        return $clientPaymentApprovalService->approve($id);

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

    public function actionPay($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->name = $model->customer->client_name;
        $model->setScenario('payMode');

        if ($model->remaining_amount == 0) {
            throw new HttpException(404, 'The requested payment id # ' . $model->client_payment_history_id . ' already paid.');
        }

        $searchModel = new SalesSearch();
        $searchModel->client_id = $model->client_id;
        $dataProvider = $searchModel->getDueSalesDataProvider();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $settlementService = new PaymentSettlementService();
            if($settlementService->settlePayment($model)){
                FlashMessage::setMessage("Payment settled successfully.", "Payment Settled", "success");
            }else{
                FlashMessage::setMessage("Payment settlement failed.", "Payment Settlement", "error");
            }
            $this->redirect(['index']);
        }

        return $this->render('pay/pay', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);

    }


    private function initializeModelDefaults(ClientPaymentHistory $model)
    {
        $model->setScenario('create');
        $model->user_id = Yii::$app->user->getId();
        $model->extra = Json::encode(['bank_id' => 0, 'branch_id' => 0]);
        $model->status = ClientPaymentHistory::STATUS_PENDING;
        if (StoreUtility::countUserStores() === 1) {
            $model->outletId = StoreUtility::getDefaultStoreByUser();
        }
        return $model;
    }

    private function hasDepositValidationError(ClientPaymentHistory $model)
    {

        $totalDues = CustomerUtility::getTotalDuesByCustomer($model->client_id);

        if (!$model->isNewRecord) $totalDues += $model->approved_amount;

        $model->extra = Json::encode(['bank_id' => null, 'branch_id' => null]);

        if ($model->paymentType && $model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
            if (empty($model->bank_id) || empty($model->branch_id)) {
                $model->payment_type_id = 0;
                $model->bank_id = 0;
                $model->branch_id = 0;
                $model->addError('bank_id', 'Bank can\'t be empty');
                $model->addError('branch_id', 'Branch can\'t be empty');
                return true;
            }

            $model->extra = Json::encode([
                'bank_id' => $model->bank_id,
                'branch_id' => $model->branch_id
            ]);
        }

        if ( $model->source === ClientPaymentHistory::RECEIVED_TYPE_DUE_RECEIVED || $model->received_type === ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION ) {
            if($model->received_amount > $totalDues) {
                $model->addError('received_amount', 'Received amount: '.$model->received_amount.' cant be greater than due amount: '.$totalDues);
                return true;
            }
        }

        return false;
    }


    /**
     * Creates a new ClientPaymentHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->initializeModelDefaults(new ClientPaymentHistory());

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->remaining_amount = $model->received_amount;

            if ($this->hasDepositValidationError($model)) {
                // Deposit validation failed, render the form again with errors
                return $this->render('create', ['model' => $model]);
            }

            $model->received_type = $model->source;

            if ($model->save()) {
                $message = "Transaction #{$model->client_payment_history_id} for '{$model->customer->client_name}' has been recorded. Awaiting approval.";
                FlashMessage::setMessage($message, "Payment Received", "success");
                return $this->redirect(['index']);
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


    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->source = $model->received_type;

        if (!$model) {
            throw new NotFoundHttpException('Payment record not found.');
        }

        $model->setScenario('update');

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->remaining_amount = $model->received_amount;

            if ($this->hasDepositValidationError($model)) {
                // Deposit validation failed, show form with errors
                return $this->render('update', ['model' => $model]);
            }

            $model->received_type = $model->source;
            $model->status = ClientPaymentHistory::STATUS_PENDING;
            if ($model->save()) {
                $message = "Transaction #{$model->client_payment_history_id} for '{$model->customer->client_name}' has been updated. Awaiting approval.";
                FlashMessage::setMessage($message, "Payment Received", "success");
                return $this->redirect(['index']);
            }
        }

        // For first time form load
        if (!empty($model->extra)) {
            $extra = Json::decode($model->extra);
            $model->bank_id = $extra['bank_id'] ?? null;
            $model->branch_id = $extra['branch_id'] ?? null;
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
