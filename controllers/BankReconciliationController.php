<?php

namespace app\controllers;

use app\components\CustomerUtility;
use app\components\FlashMessage;
use app\components\StoreUtility;
use app\components\Utility;
use app\models\CustomerAccount;
use app\models\Sales;
use app\services\ClientFinancialService;
use mdm\admin\components\Helper;
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
                    'approve' => ['POST'],
                    'delete' => ['POST']
                ]
            ]
        ];
    }


    public function actionApprove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            return ['success' => false, 'message' => 'Invalid request'];
        }

        $id = Yii::$app->request->post('id');
        $model = $this->findModel(Utility::decrypt($id));

        if (!$model) {
            return ['success' => false, 'message' => 'Reconciliation record not found'];
        }

        $userId = Yii::$app->user->getId();
        $oldApprovedAmount = $model->approved_amount ?? 0;
        $newAmount = $model->amount;

        // If amount is the same and already approved ➔ nothing to do
        if ($model->status === BankReconciliation::STATUS_APPROVED && $oldApprovedAmount == $newAmount) {
            return ['success' => false, 'message' => 'Already approved with the same amount'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $sales = Sales::findOne(['sales_id' => $model->invoice_id]);
            if (!$sales) {
                throw new \Exception('Sales record not found.');
            }

            // 1️⃣ Reverse previous approval (if any)
            if ($oldApprovedAmount != 0) {
                $sales->reconciliation_amount -= $oldApprovedAmount;
                ClientFinancialService::adjustDueReconciliation(
                    $sales->client_id,
                    - $oldApprovedAmount,
                    'Reversal of previous reconciliation (' . $oldApprovedAmount . ')',
                    $userId,
                    ClientFinancialService::REF_TABLE_RECONCILIATION,
                    $model->id,
                    true
                );
            }

            // 2️⃣ Apply new amount (even if it's higher, lower, or zero)
            $sales->reconciliation_amount += $newAmount;
            ClientFinancialService::adjustDueReconciliation(
                $sales->client_id,
                $newAmount,
                $model->remarks,
                $userId,
                ClientFinancialService::REF_TABLE_RECONCILIATION,
                $model->id,
                false
            );

            if (!$sales->save()) {
                throw new \Exception('Failed to update sales reconciliation amount.');
            }

            // 3️⃣ Update reconciliation record
            $model->status = BankReconciliation::STATUS_APPROVED;
            $model->approved_amount = $newAmount;
            $model->updated_by = $userId;
            if (!$model->save()) {
                throw new \Exception('Failed to update reconciliation record.');
            }

            $transaction->commit();

            return ['success' => true, 'message' => 'Reconciliation approved successfully'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Approval failed: ' . $e->getMessage()
            ];
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
        if(StoreUtility::countUserStores()===1){
            $model->outletId = StoreUtility::getDefaultStoreByUser();
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
                    FlashMessage::setMessage('Bank Reconciliation: ' . $model->amount . ' has been added.', "Reconciliation", "success");
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
                    FlashMessage::setMessage('Bank Reconciliation: ' . $model->amount . ' has been updated.', "Reconciliation", "success");
                    return $this->redirect(['index']);
                }
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
