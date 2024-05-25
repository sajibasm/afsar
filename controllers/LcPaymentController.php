<?php

namespace app\controllers;

use app\components\FlashMessage;
use app\components\Utility;
use app\models\CashBook;
use app\models\DepositBook;
use app\models\Expense;
use app\models\ExpenseType;
use app\models\PaymentType;
use Exception;
use mdm\admin\components\Helper;
use Yii;
use app\models\LcPayment;
use app\models\LcPaymentSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * LcPaymentController implements the CRUD actions for LcPayment model.
 */
class LcPaymentController extends Controller
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
     * Lists all LcPayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LcPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        if(!Helper::checkRoute('approved')) {
            return "You are not allowed to perform this action.";
        }

        $model = $this->findModel(Utility::decrypt($id));

        if($model->status==LcPayment::STATUS_PENDING){
            $data = json_decode($model->extra);

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
    }


    public function actionApproved($id)
    {


            $response = [];
            $hasError = false;
            $model = $this->findModel(Utility::decrypt($id));
            $data = json_decode($model->extra);

            if(isset($data->bank_id)){
                $model->bank_id = $data->bank_id;
            }

            if(isset($data->branch_id)){
                $model->branch_id = $data->branch_id;
            }


            $model->status = LcPayment::STATUS_APPROVED;
            $model->updated_by = Yii::$app->user->getId();

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if($model->save()) {

                    $expense = Expense::find()->where(['expense_type_id' => ExpenseType::TYPE_LC, 'ref_id' => $model->lc_payment_id])->one();
                    $expense->status = Expense::STATUS_APPROVED;
                    $expense->updated_by = Yii::$app->user->getId();
                    if ($expense->save()) {
                        if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                            $deposit = new DepositBook();
                            $deposit->bank_id = $model->bank_id;
                            $deposit->branch_id = $model->branch_id;
                            $deposit->payment_type_id = $model->payment_type;
                            $deposit->ref_user_id = Yii::$app->user->getId();
                            $deposit->deposit_in = 0;
                            $deposit->deposit_out = $model->amount;
                            $deposit->reference_id = $model->lc_payment_id;
                            $deposit->source = DepositBook::SOURCE_LC;
                            $deposit->remarks = $model->remarks;
                            if (!$deposit->save()) {
                                $hasError = true;
                            }
                        } else {
                            $cash = new CashBook();
                            $cash->cash_in = 0;
                            $cash->cash_out = $model->amount;
                            $cash->source = CashBook::SOURCE_LC;
                            $cash->ref_user_id = $model->user_id;
                            $cash->reference_id = $model->lc_payment_id;
                            $cash->source = CashBook::SOURCE_LC;
                            $cash->remarks = $model->remarks;
                            if (!$cash->save()) {
                                $hasError = true;
                            }
                        }
                    } else {
                        $hasError = true;
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
                $response =  ['status'=>'Has error found', 'Error'=>true];
            }else{
                $response =  ['status'=>'Done', 'Error'=>false];
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


    /**
     * Creates a new LcPayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LcPayment();
        $model->user_id = Yii::$app->user->getId();
        $model->status = LcPayment::STATUS_PENDING;
        $model->extra = Json::encode(['bank_id'=>0, 'branch_id'=>0]);

        $hasError = false;

        if (Yii::$app->request->isPost) {

            $response = [];
            $addRules =  false;

            $model->load(Yii::$app->request->post());

            $paymentType = PaymentType::findOne($model->payment_type);
            if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                $model->extra = Json::encode(['bank_id'=>$model->bank_id, 'branch_id'=>$model->branch_id]);
            }

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                    if (empty($model->bank_id) || empty($model->branch_id) ) {
                        $addRules = true;
                        $model->payment_type=0;
                        $model->bank_id=0;
                        $model->branch_id=0;
                        $model->addError('bank_id', 'Bank Can\'t be Empty');
                        $model->addError('branch_id', 'Branch Can\'t be Empty');
                    }
                }

                if($addRules==false){
                    if($model->save()) {

                        $expense = new Expense();
                        $expense->source = Expense::SOURCE_EXTERNAL;
                        $expense->status = Expense::STATUS_PENDING;
                        $expense->expense_type_id = ExpenseType::TYPE_LC;
                        $expense->ref_id = $model->lc_payment_id;
                        $expense->user_id = $model->user_id;
                        $expense->expense_amount = $model->amount;
                        $expense->expense_remarks = $model->remarks;
                        $expense->type = $model->paymentType->type;
                        if($expense->save()){
                            $transaction->commit();
                            $response = 'LC: ' . $model->lc->lc_name . ' and Amount: '.$model->amount.' has been added.';
                        }

                    }else{
                        $transaction->rollBack();
                        $response = 'LC: ' . $model->lc->lc_name . ' and Amount: '.$model->amount.' has been error occurred while adding.';
                    }
                }

            }catch (Exception $e) {
                $transaction->rollBack();
                $response = 'LC: ' . $model->lc->lc_name . ' and Amount: '.$model->amount.' has been error occurred while adding.';
                throw $e;
            }

            if($addRules==false){
                FlashMessage::setMessage($response, "LC Payment", "success");
                if(Helper::checkRoute('approved')){
                    return $this->redirect(['approved', 'id'=>Utility::encrypt($model->lc_id)]);
                }
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }


    /**
     * Updates an existing LcPayment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));

        $hasError = false;
        $expenseType = '';
        $addRules =  false;
        $response = [];

        if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
            $json = (object) Json::decode($model->extra);
            $model->bank_id = $json->bank_id;
            $model->branch_id = $json->branch_id;
        }


        if (Yii::$app->request->isPost) {

            $type = $model->paymentType->type;
            $model->load(Yii::$app->request->post());
            $model->remarks = 'Update '.$model->remarks;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
                    if (empty($model->bank_id) || empty($model->branch_id) ) {
                        $addRules = true;
                        $model->payment_type=0;
                        $model->bank_id=0;
                        $model->branch_id=0;
                        $model->addError('bank_id', 'Bank Can\'t be Empty');
                        $model->addError('branch_id', 'Branch Can\'t be Empty');
                    }
                }


                if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                    $model->extra = Json::encode(['bank_id'=>$model->bank_id, 'branch_id'=>$model->branch_id]);
                }


                if($addRules==false){
                    if($model->save()){
                        if($type==$model->paymentType->type){
                            if($type==PaymentType::TYPE_DEPOSIT){
                                $deposit = DepositBook::find()->where(['reference_id'=>$model->lc_payment_id, 'source'=>DepositBook::SOURCE_LC])->one();
                                if($deposit){
                                    $deposit->deposit_out = $model->amount;
                                    if(!$deposit->save()){
                                        $hasError = true;
                                    }
                                }

                            }else{
                                $cash = CashBook::find()->where(['reference_id'=>$model->lc_payment_id, 'source'=>CashBook::SOURCE_LC])->one();
                                if($cash){
                                    $cash->cash_out = $model->amount;
                                    if(!$cash->save()){
                                        $hasError = true;
                                    }
                                }
                            }
                        }else{

                            if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                                $cash = CashBook::find()->where(['reference_id'=>$model->lc_payment_id, 'source'=>CashBook::SOURCE_LC])->one();
                                if($cash){
                                    $cash->cash_out = 0;
                                    $cash->remarks = 'Update By: '.Yii::$app->user->identity->username;
                                    if($cash->save()){
                                        $deposit = new DepositBook();
                                        $deposit->bank_id = $model->bank_id;
                                        $deposit->branch_id = $model->branch_id;
                                        $deposit->payment_type_id = $model->payment_type;
                                        $deposit->ref_user_id = Yii::$app->user->getId();
                                        $deposit->deposit_in = 0;
                                        $deposit->deposit_out = $model->amount;
                                        $deposit->reference_id = $model->lc_payment_id;
                                        $deposit->source = DepositBook::SOURCE_LC;
                                        $deposit->remarks = $model->remarks.", CashBook(ID:{$cash->id})";
                                        if(!$deposit->save()){
                                            $hasError = true;
                                        }
                                    }
                                }

                            }else{
                                $deposit = DepositBook::find()->where(['reference_id'=>$model->lc_payment_id, 'source'=>DepositBook::SOURCE_LC])->one();
                                if($deposit){
                                    $deposit->deposit_out = 0;
                                    $deposit->remarks = 'Update By: '.Yii::$app->user->identity->username;
                                    if($deposit->save()){
                                        $cash = new CashBook();
                                        $cash->cash_in = 0;
                                        $cash->cash_out = $model->amount;
                                        $cash->source = CashBook::SOURCE_LC;
                                        $cash->reference_id = $model->lc_payment_id;
                                        $cash->remarks =$model->remarks.", BankBook(ID:{$deposit->id})";
                                        if(!$cash->save()){
                                            $hasError = true;
                                        }
                                    }
                                }
                            }
                        }

                        $expense = Expense::find()->where(['ref_id'=>$model->lc_payment_id, 'expense_type_id'=>ExpenseType::TYPE_LC])->one();
                        if($expense){
                            $expense->expense_amount = $model->amount;
                            $expense->type = $expenseType;
                            $expense->expense_remarks = $model->remarks." Update By: ".Yii::$app->user->identity->username;
                            if(!$expense->save()){
                                $hasError = true;
                            }
                        }
                    }else{
                        $hasError = true;
                    }

                    if($hasError){
                        $response = 'LC: ' . $model->lc->lc_name . ' and Amount: '.$model->amount.' has been error occurred while updating.';
                        $transaction->rollBack();
                    }else{
                        $response = 'LC: ' . $model->lc->lc_name . ' and Amount: '.$model->amount.' has been updated.';
                        $transaction->commit();
                    }
                }

            }catch (Exception $e) {
                $transaction->rollBack();
                $response = 'LC: ' . $model->lc->lc_name . ' and Amount: '.$model->amount.' has been error occurred while updating.';
                throw $e;
            }

            if($addRules==false){
                FlashMessage::setMessage($response, "LC Payment", "success");
                if(Helper::checkRoute('approved')){
                    return $this->redirect(['approved', 'id'=>Utility::encrypt($model->lc_id)]);
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
     * Finds the LcPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LcPayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LcPayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
