<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\PdfGen;
use app\components\Utility;
use app\models\Bank;
use app\models\Branch;
use app\models\CashBook;
use app\models\ClientPaymentHistory;
use app\models\DepositBook;
use app\models\PaymentType;
use app\modules\admin\components\Helper;
use Yii;
use app\models\CustomerWithdraw;
use app\models\CustomerWithdrawSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CustomerWithdrawController implements the CRUD actions for CustomerWithdraw model.
 */
class CustomerWithdrawController extends Controller
{
    /**
     * @inheritdoc
     */
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

    public function actionPrint($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
       return PdfGen::refundReceipt(Utility::decrypt($id), false);
    }


    /**
     * Lists all CustomerWithdraw models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerWithdrawSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerWithdraw model.
     * @param integer $id
     * @return mixed
     */

    public function actionApproved($id)
    {
        
            $response = [];
            $hasError = false;
            $model = $this->findModel(Utility::decrypt($id));
            $model->status = CustomerWithdraw::STATUS_APPROVED;
            $extra = Json::decode($model->extra);

            $customerPaymentHistory = ClientPaymentHistory::findOne($model->payment_history_id);

            if($extra['paymentType']==PaymentType::TYPE_CASH){
                $cash = new CashBook();
                $cash->cash_in = 0;
                $cash->cash_out = $model->amount;
                $cash->source = CashBook::SOURCE_SALES_WITHDRAW;
                $cash->reference_id = $model->id;
                $cash->remarks = $model->remarks;
                if($cash->save()) {
                    if($model->save()){
                        $customerPaymentHistory->status = ClientPaymentHistory::STATUS_APPROVED;
                        $customerPaymentHistory->remaining_amount -= $model->amount;
                        if(!$customerPaymentHistory->save()){
                            $hasError = true;
                        }
                    }
                }
            }else{
                $depositBook = new DepositBook();
                $depositBook->payment_type_id = $customerPaymentHistory->payment_type_id;
                $depositBook->bank_id = 0;
                $depositBook->branch_id = 0;
                $depositBook->deposit_in = 0;
                $depositBook->deposit_out = $model->amount;
                $depositBook->source = DepositBook::SOURCE_SALES_WITHDRAW;
                $depositBook->reference_id = $model->id;
                $depositBook->remarks = $model->remarks;
                if($depositBook->save()) {
                    if($model->save()){
                        $customerPaymentHistory->status = ClientPaymentHistory::STATUS_APPROVED;
                        $customerPaymentHistory->remaining_amount -= $model->amount;
                        if(!$customerPaymentHistory->save()){
                            $hasError = true;
                        }
                    }
                }
            }

            if($hasError){
                $response = ['status'=>'Has error found', 'Error'=>true];
            }else{
                $response  =['status'=>'Done', 'Error'=>false];
            }

            if(Yii::$app->request->isAjax){
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                $message = "Invoice# ".$model->amount." Payment Received Transaction: ".$model->payment_history_id." has been withdraw.";
                FlashMessage::setMessage($message, "Withdraw", "info");
                return $this->redirect(['/customer-withdraw/index']);
            }



    }

    public function actionView($id)
    {
        if(!Helper::checkRoute('approved')) {
            return "You are not allowed to perform this action.";
        }

        if(Yii::$app->request->isAjax){
                $model = $this->findModel(Utility::decrypt($id));
                $extra = Json::decode($model->extra);

                if($extra['paymentType']==PaymentType::TYPE_CASH){
                    $model->extra = PaymentType::TYPE_CASH;
                }else{
                    $bank = Bank::findOne($extra['bank']);
                    $branch = Branch::findOne($extra['branch']);
                    $model->extra = PaymentType::TYPE_DEPOSIT.' Bank: '.$bank->bank_name.' Branch: '.$branch->branch_name;
                }

                return $this->renderAjax('view', [
                    'model' => $model,
                ]);
        }else{
            return $this->redirect(['index']);
        }

    }

    /**
     * Creates a new CustomerWithdraw model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CustomerWithdraw();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CustomerWithdraw model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $payment = ClientPaymentHistory::find()->where(['client_payment_history_id'=>$model->payment_history_id])->one();

        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            if($model->amount>$payment->remaining_amount){
                $model->addError('amount', 'Amount should be less then '.$payment->remaining_amount);
            }else{
                if($model->save()){
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }


    /**
     * Finds the CustomerWithdraw model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerWithdraw the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerWithdraw::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
