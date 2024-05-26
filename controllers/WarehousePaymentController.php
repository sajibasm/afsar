<?php

namespace app\controllers;

use app\components\CommonUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\Bank;
use app\models\Branch;
use app\models\CashBook;
use app\models\DepositBook;
use app\models\Expense;
use app\models\ExpenseType;
use app\models\PaymentType;
use app\modules\admin\components\Helper;
use Yii;
use app\models\WarehousePayment;
use app\models\WarehousePaymentSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * WarehousePaymentController implements the CRUD actions for WarehousePayment model.
 */
class WarehousePaymentController extends Controller
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
     * Lists all WarehousePayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WarehousePaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider]);
    }


    /**
     * Creates a new WarehousePayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WarehousePayment();
        $model->status =
        $model->user_id = Yii::$app->user->getId();
        $model->status = WarehousePayment::STATUS_PENDING;
        $model->extra = Json::encode(['bank_id'=>null, 'branch_id'=>null]);
        $addRules = false;

        if (Yii::$app->request->isPost) {

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $model->load(Yii::$app->request->post());


            try {

                if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
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
                }

                if($addRules==false){
                    if($model->save()) {
                        $expense = new Expense();
                        $expense->source = Expense::SOURCE_EXTERNAL;
                        $expense->status = Expense::STATUS_PENDING;
                        $expense->expense_type_id = ExpenseType::TYPE_WAREHOUSE;
                        $expense->ref_id = $model->id;
                        $expense->user_id = $model->user_id;
                        $expense->expense_amount = $model->payment_amount;
                        $expense->expense_remarks = $model->remarks;
                        $expense->extra = $model->extra;

                        if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                            $expense->type = Expense::TYPE_DEPOSIT;
                        }else{
                            $expense->type = Expense::TYPE_CASH;
                        }

                        if($expense->save()){
                            $transaction->commit();
                        }else{
                            Utility::debug($expense->getErrors());
                        }

                    }else{
                        Utility::debug($model->getErrors());

                        $transaction->rollBack();
                    }
                }


            }catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }


            if($addRules==false){
                FlashMessage::setMessage("New Warehouse Payment Amount#{$model->payment_amount} has been added", "Warehouse Payment", "success");
                if(Helper::checkRoute('approved')){
                    return $this->redirect(['approved', 'id'=>Utility::encrypt($model->id)]);
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionApproved($id)
    {


            $hasError = false;
            $model = $this->findModel(Utility::decrypt($id));
            $data = json_decode($model->extra);
            $response = [];

            if(isset($data->bank_id)){
                $model->bank_id = $data->bank_id;
            }

            if(isset($data->branch_id)){
                $model->branch_id = $data->branch_id;
            }


            $model->status = WarehousePayment::STATUS_APPROVED;
            $model->updated_by = Yii::$app->user->getId();

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if($model->save()) {

                    $expense = Expense::find()->where(['expense_type_id' => ExpenseType::TYPE_WAREHOUSE, 'ref_id' => $model->id])->one();
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
                            $deposit->deposit_out = $model->payment_amount;
                            $deposit->reference_id = $model->id;
                            $deposit->source = DepositBook::SOURCE_WAREHOUSE;
                            $deposit->remarks = $model->remarks;
                            if (!$deposit->save()) {
                                $hasError = true;
                            }
                        } else {
                            $cash = new CashBook();
                            $cash->cash_in = 0;
                            $cash->cash_out = $model->payment_amount;
                            $cash->source = CashBook::SOURCE_WAREHOUSE;
                            $cash->ref_user_id = $model->user_id;
                            $cash->reference_id = $model->id;
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
                $response = ['status'=>'Has error found', 'Error'=>true];
            }else{
                $response = ['status'=>'Done', 'Error'=>false];
            }

        
            if(Yii::$app->request->isAjax){
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                FlashMessage::setMessage(" Warehouse Payment Amount#{$model->payment_amount} has been added", "Warehouse Payment", "success");
                return $this->redirect(['index']);
            }

    }


    public function actionView($id)
    {
        if(!Helper::checkRoute('approved')) {
            return "You are not allowed to perform this action.";
        }

        $model = $this->findModel(Utility::decrypt($id));

        if($model->status==WarehousePayment::STATUS_PENDING){
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

    /**
     * Updates an existing WarehousePayment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $hasError = false;
        $expenseType = '';
        $addRules = false;
        $model = $this->findModel(Utility::decrypt($id));

        if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
            $json = (object) Json::decode($model->extra);
            $model->bank_id = $json->bank_id;
            $model->branch_id = $json->branch_id;
        }


        if (Yii::$app->request->isPost) {

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $model->load(Yii::$app->request->post());


            try {

                $type = $model->paymentType->type;

                if ($model->paymentType->type == PaymentType::TYPE_DEPOSIT) {
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
                }


                if($addRules==false){
                    if($model->save()) {
                        if($type==$model->paymentType->type){
                            if($type==PaymentType::TYPE_DEPOSIT){
                                $deposit = DepositBook::find()->where(['reference_id'=>$model->id , 'source'=>DepositBook::SOURCE_WAREHOUSE])->one();
                                if($deposit){
                                    $deposit->deposit_out = $model->payment_amount;
                                    if(!$deposit->save()){
                                        $hasError = true;
                                    }
                                }
                            }else{
                                $cash = CashBook::find()->where(['reference_id'=>$model->id, 'source'=>CashBook::SOURCE_WAREHOUSE])->one();
                                if($cash){
                                    $cash->cash_out = $model->payment_amount;
                                    if(!$cash->save()){
                                        $hasError = true;
                                    }
                                }
                            }
                        }else{
                            if($model->paymentType->type==PaymentType::TYPE_DEPOSIT){
                                $cash = CashBook::find()->where(['reference_id'=>$model->id, 'source'=>CashBook::SOURCE_WAREHOUSE])->one();
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
                                        $deposit->deposit_out = $model->payment_amount;
                                        $deposit->reference_id = $model->id;
                                        $deposit->source = DepositBook::SOURCE_WAREHOUSE;
                                        $deposit->remarks = $model->remarks.", CashBook(ID:{$cash->id})";
                                        if(!$deposit->save()){
                                            $hasError = true;
                                        }
                                    }
                                }

                            }else{
                                $deposit = DepositBook::find()->where(['reference_id'=>$model->id, 'source'=>CashBook::SOURCE_WAREHOUSE])->one();
                                if($deposit){
                                    $deposit->deposit_out = 0;
                                    $deposit->remarks = 'Update By: '.Yii::$app->user->identity->username;
                                    if($deposit->save()){
                                        $cash = new CashBook();
                                        $cash->cash_in = 0;
                                        $cash->cash_out = $model->payment_amount;
                                        $cash->source = CashBook::SOURCE_WAREHOUSE;
                                        $cash->reference_id = $model->id;
                                        $cash->remarks =$model->remarks.", BankBook(ID:{$deposit->id})";
                                        if(!$cash->save()){
                                            $hasError = true;
                                        }
                                    }
                                }
                            }
                        }

                        $expense = Expense::find()->where(['ref_id'=>$model->id, 'expense_type_id'=>ExpenseType::TYPE_WAREHOUSE])->one();

                        if($expense){
                            $expense->expense_amount = $model->payment_amount;
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
                        $transaction->rollBack();
                    }else{
                        $transaction->commit();
                    }
                }

            }catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            if($addRules==false){
                FlashMessage::setMessage(" Warehouse Payment Amount#{$model->payment_amount} has been updated", "Warehouse Payment", "success");
                if(Helper::checkRoute('approved')){
                    return $this->redirect(['approved', 'id'=>Utility::encrypt($model->id)]);
                }
                return $this->redirect(['index']);
            }

        }


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the WarehousePayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WarehousePayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WarehousePayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
