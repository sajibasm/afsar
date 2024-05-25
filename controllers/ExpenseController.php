<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\PdfGen;
use app\components\Utility;
use app\models\CashBook;
use app\models\DepositBook;
use app\models\PaymentType;
use Exception;
use mdm\admin\components\Helper;
use Yii;
use app\models\Expense;
use app\models\ExpenseSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ExpenseController implements the CRUD actions for Expense model.
 */
class ExpenseController extends Controller
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


    public function actionInvoice($id)
    {
        PdfGen::expenseInvoice($id, false);
    }

    /**
     * Lists all Expense models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExpenseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
            if(Yii::$app->request->isAjax){
                $model = $this->findModel(Utility::decrypt($id));
                if($model->status==Expense::STATUS_PENDING){
                    $data = Json::decode($model->extra);
                    if(isset($data->bank_id)){
                        $model->bank_id = $data->bank_id;
                    }
                    if(isset($data->branch_id)){
                        $model->branch_id = $data->branch_id;
                    }

                    return $this->renderAjax('view', [
                        'model' => $model,
                    ]);

            }
        }else{
            return $this->redirect(['index']);
        }
    }


    public function actionApproved($id)
    {
        $response = [];
        $hasError = false;
        $model = $this->findModel(Utility::decrypt($id));

        if($model){
            if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                $json = (object) Json::decode($model->extra);
                $model->bank_id = $json->bank_id;
                $model->branch_id = $json->branch_id;
            }

            $model->status = Expense::STATUS_APPROVED;
            $model->updated_by = Yii::$app->user->getId();

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {
                if($model->save()) {
                    $paymentType = PaymentType::find()->where(['payment_type_name'=>$model->type])->one();
                    if ($model->type == PaymentType::TYPE_DEPOSIT) {
                        $deposit = new DepositBook();
                        $deposit->outletId = $model->outletId;
                        $deposit->bank_id = $model->bank_id;
                        $deposit->branch_id = $model->branch_id;
                        $deposit->payment_type_id = $paymentType->payment_type_id;
                        $deposit->ref_user_id = Yii::$app->user->getId();
                        $deposit->deposit_in = 0;
                        $deposit->deposit_out = $model->expense_amount;
                        $deposit->reference_id = $model->expense_id;
                        $deposit->source = DepositBook::SOURCE_DAILY_EXPENSE;
                        $deposit->remarks = $model->expense_remarks;
                        if (!$deposit->save()) {
                            $hasError = true;
                        }
                    } else {
                        $cash = new CashBook();
                        $cash->outletId = $model->outletId;
                        $cash->cash_in = 0;
                        $cash->cash_out = $model->expense_amount;
                        $cash->source = CashBook::SOURCE_DAILY_EXPENSE;
                        $cash->ref_user_id = Yii::$app->user->getId();
                        $cash->reference_id = $model->expense_id;
                        $cash->remarks = $model->expense_remarks;
                        if (!$cash->save()) {
                            $hasError = true;
                        }

                    }
                }

                if($hasError){
                    $transaction->rollBack();
                }else{
                    $transaction->commit();
                }

            }catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            if($hasError){
                $response = ['status'=>'Has error found', 'Error'=>true];
            }else{
                $response = ['status'=>'Done', 'Error'=>false];
            }

            if(Yii::$app->request->isAjax){
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                $message = "Expense #" . $model->expense_amount . " has been approved.";
                FlashMessage::setMessage($message, "Approved Expense", "info");
                return $this->redirect(['index']);
            }
        }else{
            $message = "Something went wrong.";
            FlashMessage::setMessage($message, "Approved Expense", "danger");
            return $this->redirect(['index']);
        }

    }

    /**
     * Creates a new Expense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Expense();
        $model->source = Expense::SOURCE_INTERNAL;
        $model->user_id = Yii::$app->user->getId();
        $model->status = Expense::STATUS_PENDING;
        $model->extra = Json::encode(['bank_id'=>0, 'branch_id'=>0]);
        $addRules =  false;

        if(Yii::$app->request->isPost){

            $model->load(Yii::$app->request->post());

            $paymentType = PaymentType::findOne($model->payment_type);

            if ($paymentType->type==PaymentType::TYPE_DEPOSIT) {
                $model->type = PaymentType::TYPE_DEPOSIT;
                if (empty($model->bank_id) || empty($model->branch_id) ) {
                    $addRules = true;
                    $model->payment_type=0;
                    $model->bank_id=0;
                    $model->branch_id=0;
                    $model->addError('bank_id', 'Bank Can\'t be Empty');
                    $model->addError('branch_id', 'Branch Can\'t be Empty');
                }else{
                    $model->extra = Json::encode(['bank_id'=>$model->bank_id, 'branch_id'=>$model->branch_id]);
                }
            }else{
                $model->type = PaymentType::TYPE_CASH;
            }

            if($addRules==false){
                if($model->save()) {
                    $message = 'Expense Amount: '.$model->expense_amount.' has been added.';
                    FlashMessage::setMessage($message, "New Expense", "success");
                    if(Yii::$app->asm->can('approved')){
                       return $this->redirect(['approved', 'id'=>Utility::encrypt($model->expense_id)]);
                    }

                    return $this->redirect(['index']);

                }else{
                    $message = 'Expense Errors';
                    FlashMessage::setMessage($message, "New Expense", "danger");
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Expense model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $hasError = false;
        $model = $this->findModel(Utility::decrypt($id));
        $model->status = Expense::STATUS_PENDING;
        $paymentType = PaymentType::find()->where(['payment_type_name'=>$model->type])->one();
        $model->payment_type = $paymentType->payment_type_id;
        $addRules =  false;
        $message = null;

        if($model->type==PaymentType::TYPE_DEPOSIT){
            $json = (object) Json::decode($model->extra);
            $model->bank_id = $json->bank_id;
            $model->branch_id = $json->branch_id;
        }

        if (Yii::$app->request->isPost) {

            $type = $model->type;
            $model->load(Yii::$app->request->post());
            $model->expense_remarks = 'Update '.$model->expense_remarks;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                    $model->type = PaymentType::TYPE_DEPOSIT;
                    if (empty($model->bank_id) || empty($model->branch_id) ) {
                        $addRules = true;
                        $model->bank_id=0;
                        $model->branch_id=0;
                        $model->payment_type=0;
                        $model->addError('bank_id', 'Bank Can\'t be Empty');
                        $model->addError('branch_id', 'Branch Can\'t be Empty');
                    }else{
                        $model->extra = Json::encode(['bank_id'=>$model->bank_id, 'branch_id'=>$model->branch_id]);
                    }
                }else{
                    $model->type = PaymentType::TYPE_CASH;
                }

                if($addRules==false){

                    if($model->save()){
                        if($type==$model->paymentType->type){
                            if($type==PaymentType::TYPE_DEPOSIT){
                                $deposit = DepositBook::find()->where(['reference_id'=>$model->expense_id, 'source'=>DepositBook::SOURCE_DAILY_EXPENSE])->one();
                                if($deposit){
                                    $deposit->deposit_out = $model->expense_amount;
                                    $deposit->remarks = $model->expense_remarks;
                                    if(!$deposit->save()){
                                        $hasError = true;
                                    }
                                }

                            }else{
                                $cash = CashBook::find()->where(['reference_id'=>$model->expense_id, 'source'=>CashBook::SOURCE_DAILY_EXPENSE])->one();
                                if($cash){
                                    $cash->remarks = $model->expense_remarks;
                                    $cash->cash_out = $model->expense_amount;
                                    if(!$cash->save()){
                                        $hasError = true;
                                    }
                                }
                            }
                        }else{
                            if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                                $cash = CashBook::find()->where(['reference_id'=>$model->expense_id, 'source'=>CashBook::SOURCE_DAILY_EXPENSE])->one();
                                if($cash){
                                    $cash->cash_out = 0;
                                    $cash->remarks = 'Update By: '.Yii::$app->user->identity->username;
                                    if($cash->save()){
                                        $deposit = new DepositBook();
                                        $deposit->outletId = $model->outletId;
                                        $deposit->bank_id = $model->bank_id;
                                        $deposit->branch_id = $model->branch_id;
                                        $deposit->payment_type_id = $paymentType->payment_type_id;
                                        $deposit->ref_user_id = Yii::$app->user->getId();
                                        $deposit->deposit_in = 0;
                                        $deposit->deposit_out = $model->expense_amount;
                                        $deposit->reference_id = $model->expense_id;
                                        $deposit->source = DepositBook::SOURCE_DAILY_EXPENSE;
                                        $deposit->remarks = $model->expense_remarks.", CashBook(ID:{$cash->id})";
                                        if(!$deposit->save()){
                                            $hasError = true;
                                        }
                                    }
                                }

                            }else{
                                $deposit = DepositBook::find()->where(['reference_id'=>$model->expense_id, 'source'=>DepositBook::SOURCE_DAILY_EXPENSE])->one();
                                if($deposit){
                                    $deposit->deposit_out = 0;
                                    $deposit->remarks = 'Update By: '.Yii::$app->user->identity->username;
                                    if($deposit->save()){
                                        $cash = new CashBook();
                                        $cash->outletId = $model->outletId;
                                        $cash->cash_in = 0;
                                        $cash->cash_out = $model->expense_amount;
                                        $cash->source = CashBook::SOURCE_DAILY_EXPENSE;
                                        $cash->reference_id = $model->expense_id;
                                        $cash->remarks =$model->expense_remarks.", BankBook(ID:{$deposit->id})";
                                        if(!$cash->save()){
                                            $hasError = true;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $hasError = true;
                    }

                    if($hasError){
                        $message = 'Type: '.$model->expenseType->expense_type_name . ' and Amount:'.$model->expense_amount.' update has been rollback.';
                        $transaction->rollBack();
                    }else{
                        $message = 'Type: '.$model->expenseType->expense_type_name . ' and Amount:'.$model->expense_amount.' has been updated.';
                        $transaction->commit();
                    }
                }

            }catch (Exception $e) {
                $transaction->rollBack();
                $message = 'Type: '.$model->expenseType->expense_type_name . ' and Amount:'.$model->expense_amount.' update has been rollback.';
                throw $e;
            }

            if($addRules==false && $hasError==false){
                FlashMessage::setMessage($message, "Update Expense", "success");
                if(Yii::$app->asm->can('approved')){
                    return $this->redirect(['approved', 'id'=>Utility::encrypt($model->expense_id)]);
                }
                return $this->redirect(['index']);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }








    }


    /**
     * Finds the Expense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
