<?php

namespace app\controllers;

use app\components\CustomerUtility;
use app\components\FlashMessage;
use app\components\OutletUtility;
use app\components\Utility;
use app\models\CashBook;
use app\models\CustomerAccount;
use app\models\DepositBook;
use app\models\Expense;
use app\models\ExpenseType;
use app\models\PaymentType;
use app\models\Sales;
use app\modules\admin\components\Helper;
use Yii;
use app\models\BankReconciliation;
use app\models\BankReconciliationSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * BankReconciliationController implements the CRUD actions for BankReconciliation model.
 */
class BankReconciliationController extends Controller
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
        }
        return Yii::$app->user->isGuest? $this->redirect(['/site/login']): $this->redirect(['/site/permission']);
    }

    public function actionApproved($id)
    {
        $response = [];

        $model = $this->findModel(Utility::decrypt($id));

        if($model){
            $model->status = BankReconciliation::STATUS_APPROVED;
            $model->updated_by = Yii::$app->user->getId();
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                if ($model->save()) {
                    $sales = Sales::find()->where(['sales_id'=>$model->invoice_id])->one();
                    $sales->reconciliation_amount = $model->amount;
                    if($sales->save()){
                        $account = CustomerAccount::find()->where(['sales_id'=>$model->invoice_id])->orderBy('id DESC')->one();

                        $customerAccount = new CustomerAccount();
                        $customerAccount->client_id = $sales->client_id;
                        $customerAccount->sales_id =  $sales->sales_id;
                        $customerAccount->memo_id =  $sales->memo_id?$sales->memo_id:null;
                        $customerAccount->type =  CustomerAccount::TYPE_RECONCILIATION;
                        $customerAccount->payment_type =  CustomerAccount::PAYMENT_TYPE_NA;
                        $customerAccount->payment_history_id = null;
                        $customerAccount->account =  CustomerAccount::ACCOUNT_RECONCILIATION;
                        $customerAccount->debit =  0;
                        $customerAccount->credit =  $model->amount;
                        $customerAccount->balance = $account->balance - $model->amount;
                        if($customerAccount->save()){
                            $transaction->commit();
                            $response =  ['status' => 'Done', 'Error' => false];
                        }else{
                            $transaction->rollBack();
                            $response = ['status' => 'Has error found', 'Error' => true];
                        }
                    }else{
                        $transaction->rollBack();
                        $response = ['status' => 'Has error found', 'Error' => true];
                    }
                }else{
                    $transaction->rollBack();
                    $response = ['status' => 'Has error found', 'Error' => true];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $response = ['status' => 'Has error found', 'Error' => true];
            }

            if (Yii::$app->request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                FlashMessage::setMessage("Reconciliation has been approved ", "Update Reconciliation", "success");
                return $this->redirect(['index']);
            }
        }else{
            FlashMessage::setMessage("Something wrong, no reconciliation has been found.", "Update Reconciliation", "success");
            return $this->redirect(['index']);
        }
        
    }

    /**
     * Finds the BankReconciliation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BankReconciliation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionView($id)
    {
        if (Yii::$app->asm->can('approved')) {
            return "You are not allowed to perform this action.";
        }

        if(Yii::$app->request->isAjax){
            $model = $this->findModel(Utility::decrypt($id));
            if($model->status===Expense::STATUS_PENDING){
                return $this->renderAjax('view', [
                    'model' => $model,
                ]);

            }
        }else{
            return $this->redirect(['index']);
        }
    }


    /**
     * Lists all BankReconciliation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankReconciliationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetInvoice()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $customerId = $parents[0];
                $out = CustomerUtility::getDuesInvoiceByCustomer($customerId);
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Creates a new BankReconciliation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BankReconciliation();
        if(OutletUtility::numberOfOutletByUser()===1){
            $model->outletId = OutletUtility::defaultOutletByUser();
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->user_id = Yii::$app->user->getId();
            $model->updated_by = $model->user_id;
            $model->status = BankReconciliation::STATUS_PENDING;

            $sales = Sales::find()->where(['sales_id'=>$model->invoice_id])->one();
            $totalDue = $sales->total_amount - $sales->received_amount;

            if($model->amount>$totalDue){
                $model->addError('amount', "Invoice# ".$model->invoice_id." maximum acceptable amount is ".$totalDue);
            }else{
                if ($model->save()) {
                    $message = 'Bank Reconciliation: ' . $model->amount . ' has been added.';

                    FlashMessage::setMessage($message, "Reconciliation", "success");
                    if (Yii::$app->asm->can('approved')) {
                        return $this->redirect(['approved', 'id' => Utility::encrypt($model->id)]);
                    }
                    return $this->redirect(['index']);
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing BankReconciliation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Utility::decrypt($id));
        $model->bank_id = null;
        $model->branch_id = null;

        if($model->status!=BankReconciliation::STATUS_PENDING){
            throw new NotFoundHttpException('After approved this record can not editable');
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->updated_by =Yii::$app->user->getId();
            $model->status = BankReconciliation::STATUS_PENDING;
            if ($model->save()) {
                $message = 'Bank Reconciliation: ' . $model->amount . ' has been updated.';
                FlashMessage::setMessage($message, "Reconciliation", "info");

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

    protected function findModel($id)
    {
        if (($model = BankReconciliation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
