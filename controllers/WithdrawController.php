<?php

namespace app\controllers;

use app\components\CashUtility;
use app\components\DepositUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\CashBook;
use app\models\DepositBook;
use app\models\PaymentType;
use mdm\admin\components\Helper;
use Yii;
use app\models\Withdraw;
use app\models\WithdrawSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * WithdrawController implements the CRUD actions for Withdraw model.
 */
class WithdrawController extends Controller
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

    public function actionApproved($id)
    {
        $response = [];
        $hasError = false;
        $hasMessage = null;

        $model = $this->findModel(Utility::decrypt($id));
        $model->status = Withdraw::STATUS_APPROVED;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {


            if ($model->save()) {

                if ($model->type == PaymentType::TYPE_DEPOSIT) {

                    $deposit = DepositBook::find()->where(['reference_id'=>$model->id, 'source'=>DepositBook::SOURCE_WITHDRAW])->one();
                    if(!$deposit){
                        $deposit = new DepositBook();
                    }
                    $deposit->outletId = $model->outletId;
                    $deposit->bank_id = $model->bank_id;
                    $deposit->branch_id = $model->branch_id;
                    $deposit->payment_type_id = $model->paymentType->payment_type_id;
                    $deposit->ref_user_id = $model->user_id;
                    $deposit->deposit_in = 0;
                    $deposit->deposit_out = $model->withdraw_amount;
                    $deposit->reference_id = $model->id;
                    $deposit->source = DepositBook::SOURCE_WITHDRAW;
                    $deposit->remarks = $model->remarks;

                    if (!$deposit->save()) {
                        $hasError = true;
                        $hasMessage = $deposit->getErrors();
                    }else{
                        $cash = CashBook::find()->where(['reference_id'=>$model->id, 'source'=>CashBook::SOURCE_WITHDRAW])->one();
                        if($cash && !$cash->delete()){
                            $hasError = true;
                            $hasMessage = "Unable to delete cashbook, which was created by previous transaction";
                        }
                    }
                } else {

                    $cash = CashBook::find()->where(['reference_id'=>$model->id, 'source'=>CashBook::SOURCE_WITHDRAW])->one();

                    if(!$cash){
                        $cash = new CashBook();
                    }

                    $cash->outletId = $model->outletId;
                    $cash->cash_in = 0;
                    $cash->cash_out = $model->withdraw_amount;
                    $cash->source = CashBook::SOURCE_WITHDRAW;
                    $cash->ref_user_id = $model->user_id;
                    $cash->reference_id = $model->id;
                    $cash->remarks = $model->remarks;
                    if ($cash->save()) {
                        $deposit = DepositBook::find()->where(['reference_id'=>$model->id, 'source'=>DepositBook::SOURCE_WITHDRAW])->one();
                        if($deposit && !$deposit->delete()){
                            $hasError = true;
                            $hasMessage = "Unable to delete deposit, which was created by previous transaction";
                        }
                    }else{
                        $hasError = true;
                        $hasMessage = $cash->getErrors();
                    }
                }

            }else{
                $hasMessage = $model->getErrors();
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        if($hasError==false){
            $response = ['status'=>'Transaction was cool', 'Error'=>false, "details"=>$hasMessage];
            $transaction->commit();
        }else{
            $response = ['status'=>'Has error found', 'Error'=>true, "details"=>$hasMessage];
            $transaction->rollBack();
        }

        if(Yii::$app->request->isAjax){
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }else{
            FlashMessage::setMessage(" Account's withdrawn amount#{$model->withdraw_amount} has been approved", "Account withdraw", "success");
            return $this->redirect(['index']);
        }

    }

    public function actionView($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        if($model->status==Withdraw::STATUS_PENDING){
            return $this->renderAjax('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Lists all Withdraw models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WithdrawSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Withdraw model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $hasError = false;
        $addRules = false;

        $model = new Withdraw();
        $model->setScenario('create');
        $model->status = Withdraw::STATUS_PENDING;
        $model->user_id = Yii::$app->user->getId();

        if (Yii::$app->request->isPost) {

                $model->load(Yii::$app->request->post());
                $paymentType = PaymentType::findOne($model->type_id);
                $model->type = $paymentType->type;
                if($model->type===PaymentType::TYPE_CASH){
                    $balance = CashUtility::getAvailableCash($model->outletId);
                    if($model->withdraw_amount>$balance){
                        $addRules = true;
                        $model->addError('withdraw_amount', "Amount must be less than or equal to {$balance}");
                    }
                }else{
                    if (empty($model->bank_id) && empty($model->branch_id) ) {
                        $addRules = true;
                        $model->type_id=0;
                        $model->addError('bank_id', 'Bank Can\'t be Empty');
                        $model->addError('branch_id', 'Branch Can\'t be Empty');
                    }
                }



            if($addRules==false){
                if ($model->save()) {
                    $message = "Transaction ID# " . $model->id . " Amount: " . $model->withdraw_amount . " has been created for withdrew.";
                    FlashMessage::setMessage($message, "Create Withdraw", "success");
                    if(Yii::$app->asm->can('approved')){
                        return $this->redirect(['approved', 'id'=>Utility::encrypt($model->id)]);
                    }else{
                        return $this->redirect(['index']);
                    }
                }else{
                    Utility::debug($model->getErrors());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Withdraw model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $addRules = false;
        $model = $this->findModel(Utility::decrypt($id));
        $model->setScenario('update');
        $model->status = Withdraw::STATUS_PENDING;
        $model->user_id = Yii::$app->user->getId();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $paymentType = PaymentType::findOne($model->type_id);
            $model->type = $paymentType->type;

            if ($model->type==PaymentType::TYPE_DEPOSIT) {
                if (empty($model->bank_id) || empty($model->branch_id) ) {
                    $addRules = true;
                    $model->type_id=0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                }
            }else{
                $model->bank_id = 0;
                $model->branch_id = 0;
            }

            if($addRules==false){
                if ($model->save()) {
                    $message = "Transaction ID# " . $model->id . " Amount: " . $model->withdraw_amount . " has been updated for withdrew.";
                    FlashMessage::setMessage($message, "Create Withdraw", "warning");
                    if(Yii::$app->asm->can('approved')){
                        return $this->redirect(['approved', 'id'=>Utility::encrypt($model->id)]);
                    }else{
                        return $this->redirect(['index']);
                    }
                }else{
                    Utility::debug($model->getErrors());
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Finds the Withdraw model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Withdraw the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Withdraw::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
