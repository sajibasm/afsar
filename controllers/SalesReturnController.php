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
use app\models\ProductStatementOutlet;
use app\models\ReturnDraft;
use app\models\ReturnDraftSearch;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\SalesDetailsSearch;
use app\models\SalesDraft;
use app\models\SalesReturnDetails;
use app\models\SalesReturnDetailsSearch;
use mdm\admin\components\Helper;
use Yii;
use app\models\SalesReturn;
use app\models\SalesReturnSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\VarDumper;
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
        $model = $this->findModel(Utility::decrypt($id));
        if ($model->status == SalesReturn::STATUS_PENDING) {
            return $this->renderPartial('view', [
                'model' => $model,
            ]);

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

    public function actionItemsRemove($id)
    {
        return ReturnDraft::findOne(Utility::decrypt($id))->delete();
    }

    public function actionApproved($id)
    {
        $response = ['error' => false, 'message' => 'Sales return approved successfully.'];

        $salesReturnId = Utility::decrypt($id);
        $salesReturnRecord = SalesReturn::findOne($salesReturnId);
        if (!$salesReturnRecord) {
            throw new NotFoundHttpException('Sales return not found.');
        }


        $salesRecord = Sales::findOne(['sales_id' => $salesReturnRecord->sales_id]);
        $salesReturnDetails = SalesReturnDetails::findAll(['sales_return_id' => $salesReturnRecord->sales_return_id]);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($salesReturnRecord->type === SalesReturn::TYPE_RETURN) {
                // Batch insert product statements
                $rows = [];
                foreach ($salesReturnDetails as $product) {
                    $rows[] = [
                        'outlet_id'     => $salesRecord->outletId,
                        'item_id'       => $product->item_id,
                        'brand_id'      => $product->brand_id,
                        'size_id'       => $product->size_id,
                        'quantity'      => $product->quantity,
                        'type'          => ProductStatementOutlet::TYPE_SALES_RETURN, // Make sure this constant is defined in the model
                        'remarks'       => $salesRecord->remarks,
                        'reference_id'  => $salesReturnRecord->sales_return_id,
                        'user_id'       => Yii::$app->user->id,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    ];
                }

                if (!empty($rows)) {
                    $columns = [
                        'outlet_id',
                        'item_id',
                        'brand_id',
                        'size_id',
                        'quantity',
                        'type',
                        'remarks',
                        'reference_id',
                        'user_id',
                        'created_at',
                        'updated_at'
                    ];

                    $inserted = Yii::$app->db->createCommand()
                        ->batchInsert(ProductStatementOutlet::tableName(), $columns, $rows)
                        ->execute();

                    if ($inserted !== count($rows)) {
                        throw new \Exception('Failed to insert all ProductStatementOutlet records.');
                    }
                }

                // Handle refund amount
                if ($salesReturnRecord->cut_off_amount > 0) {
                    $paymentHistory = new ClientPaymentHistory([
                        'sales_id' => $salesReturnRecord->sales_id,
                        'client_id' => $salesRecord->client_id,
                        'user_id' => Yii::$app->user->id,
                        'received_type' => ClientPaymentHistory::RECEIVED_TYPE_SALES_RETURN,
                        'received_amount' => $salesReturnRecord->refund_amount,
                        'remaining_amount' => $salesReturnRecord->refund_amount,
                        'remarks' => $salesReturnRecord->remarks,
                        'status' => ClientPaymentHistory::STATUS_APPROVED,
                        'updated_by' => Yii::$app->user->id,
                        'payment_type_id' => PaymentType::TYPE_SALES_RETURN_ID,
                    ]);

                    if (!$paymentHistory->save()) {
                        throw new \Exception('Failed to save payment history: ' . json_encode($paymentHistory->getErrors()));
                    }

                    $salesReturnRecord->payment_history_id = $paymentHistory->client_payment_history_id;

                    // Update sales record
                    $salesRecord->sales_return_amount += $salesReturnRecord->cut_off_amount;
                    if (!$salesRecord->save()) {
                        throw new \Exception('Failed to update sales record: ' . json_encode($salesRecord->getErrors()));
                    }
                }

                // Finalize sales return
                $salesReturnRecord->status = SalesReturn::STATUS_APPROVED;
                $salesReturnRecord->updated_by = Yii::$app->user->id;

                if (!$salesReturnRecord->save()) {
                    throw new \Exception('Failed to approve sales return: ' . json_encode($salesReturnRecord->getErrors()));
                }
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            $response = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        // Response handling
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

        if ($response['error']) {
            FlashMessage::setMessage($response['message'], 'Sales Return Error', 'danger');
        } else {
            FlashMessage::setMessage(
                'Sales Return Invoice#' . trim($salesReturnRecord->sales_id) . ' has been approved.',
                'Sales Return Approved',
                'success'
            );
        }

        return $this->redirect(['index']);
    }

    private function checkReturnableInvoice($salesId, $customerId)
    {
        $salesInvoiceModel = Sales::findOne($salesId);
        if (!$salesInvoiceModel) {
            FlashMessage::setMessage(
                "Sales Invoice #{$salesId} not found.",
                'Sales Return',
                'danger'
            );
            return false;
        }


        // Get total refund + cutoff for that invoice
        $totals = SalesReturn::getTotalRefundAndCutoffBySalesId($salesId);
        $refundedTotal = (float)$totals['total_amount'] ?? 0;
        $paidTotal = (int) ($salesInvoiceModel->total_amount-$salesInvoiceModel->discount_amount);
        if ($refundedTotal >= $paidTotal) {
            FlashMessage::setMessage(
                "Sales Invoice #{$salesId} has been fully adjusted with the paid amount.",
                'Sales Return',
                'danger'
            );
            return false;
        }

        // Check for any pending return
        $pendingReturn = SalesReturn::find()
            ->where(['sales_id' => $salesId, 'status' => SalesReturn::STATUS_PENDING])
            ->exists();

        if ($pendingReturn) {
            FlashMessage::setMessage(
                "Sales Invoice #{$salesId} already has a pending return.",
                'Sales Return',
                'danger'
            );
            return false;
        }

        return true;
    }

    public function actionCreate()
    {

        $storeIdEncrypted = Yii::$app->request->get('id');
        $salesId = $storeIdEncrypted ? Utility::decrypt($storeIdEncrypted) : null;

        if (empty($salesId) || !is_numeric($salesId)) {
            $model = new SalesReturn();
            $model->setScenario('verify');
            if (OutletUtility::numberOfOutletByUser() === 1) {
                $model->outletId = OutletUtility::defaultOutletByUser();
            }

            if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                ReturnDraft::deleteAll(['user_id' => Yii::$app->user->id]);
                $this->redirect(['create', 'id' => Utility::encrypt($model->sales_id)]);
            }

            return $this->render('customer', [
                'model' => $model,
            ]);
        }


        if (!$this->checkReturnableInvoice($salesId, Yii::$app->user->getId())) {
            return $this->redirect(['index']);
        }

        $salesInvoiceModel = Sales::find()->where(['sales_id' => $salesId])->one();

        $salesSearchModel = new SalesDetailsSearch();
        $salesSearchModel->sales_id = $salesId;
        $salesDataProvider = $salesSearchModel->searchForReturn(Yii::$app->request->queryParams);


        $returnDraftSearchModel = new ReturnDraftSearch();
        $returnDraftSearchModel->sales_id = $salesId;
        $returnDataProvider = $returnDraftSearchModel->search(Yii::$app->request->queryParams);

        $salesReturnModel = new SalesReturn();
        $salesReturnModel->user_id = Yii::$app->user->getId();
        $salesReturnModel->type = SalesReturn::TYPE_RETURN;

        $bankReconciliation = BankReconciliation::find()->where(['invoice_id' => $salesId])->one();
        if ($bankReconciliation) {
            $salesInvoiceModel->reconciliationAmount = (int)($bankReconciliation->amount);
        }

        $itemWiseTotalRefund = ReturnDraft::getTotal($salesId);

        // Step 2: Calculate how much is still due
        $remainingDues = max(0,
            $salesInvoiceModel->total_amount
            - $salesInvoiceModel->discount_amount
            - ((int)$salesInvoiceModel->received_amount + $salesInvoiceModel->reconciliation_amount)
            + $salesInvoiceModel->sales_return_amount
        );


        // Step 3: Total returns after this one
        $futureTotalReturns = $salesInvoiceModel->sales_return_amount + $itemWiseTotalRefund;

        // Step 4: Check that return doesn't exceed total invoice
        $invoiceNetTotal = $salesInvoiceModel->total_amount - $salesInvoiceModel->discount_amount;
        if ($futureTotalReturns > $invoiceNetTotal) {
            throw new \Exception("Return not allowed. Total returns exceed the invoice value.");
        }


        // Step 5: Apply values
        $salesReturnModel->cut_off_amount = $itemWiseTotalRefund; // Always full return value
        if($remainingDues>$itemWiseTotalRefund){
            $salesReturnModel->refund_amount = $itemWiseTotalRefund; // Only amount beyond dues
        }else{
            $salesReturnModel->refund_amount = max(0, $itemWiseTotalRefund - $remainingDues); // Only amount beyond dues
        }

        $salesReturnModel->total_amount = $itemWiseTotalRefund;
        $salesReturnModel->status = SalesReturn::STATUS_PENDING;
        $salesReturnModel->client_name = $salesInvoiceModel->client->client_name;
        $salesReturnModel->client_id = $salesInvoiceModel->client->client_id;
        $salesReturnModel->memo_id = $salesInvoiceModel->memo_id;
        $salesReturnModel->client_mobile = $salesInvoiceModel->client_mobile;
        $salesReturnModel->sales_id = $salesId;

        //dd($salesReturnModel);

        $products = ReturnDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'sales_id' => $salesReturnModel->sales_id])->all();

        if (Yii::$app->request->isPost) {

            $salesReturnModel->load(Yii::$app->request->post());
            $salesReturnModel->outletId = $salesReturnModel->sales->outletId;
            $salesReturnModel->status = SalesReturn::STATUS_PENDING;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($products) {
                    if ($salesReturnModel->save()) {
                        $salesReturnDetailsRows = [];
                        foreach ($products as $product) {
                            $salesDetailItems = SalesDetails::find()->where(['sales_id' => $salesId, 'size_id' => $product->size_id])->one();
                            $salesReturnDetailsRows[] = [
                                'sales_return_details_id' => null,
                                'sales_return_id' => $salesReturnModel->sales_return_id,
                                'sales_id' => $salesReturnModel->sales_id,
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

                            FlashMessage::setMessage(
                                "Sales Return has been created for Invoice# {$salesReturnModel->sales_id}",
                                "Sales Return Created",
                                "success");
                            if (Helper::checkRoute('approved')) {
                                return $this->redirect([
                                    'approved',
                                    'id' => Utility::encrypt($salesReturnModel->sales_return_id)
                                ]);
                            }
                            return $this->redirect(['index']);
                        }
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }


        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('return/index', [
                'salesInvoiceModel' => $salesInvoiceModel,
                'salesReturnModel' => $salesReturnModel,
                'salesDataProvider' => $salesDataProvider,
                'returnDataProvider' => $returnDataProvider,
            ]);
        } else {

            return $this->render('return/index', [
                'salesInvoiceModel' => $salesInvoiceModel,
                'salesReturnModel' => $salesReturnModel,
                'salesDataProvider' => $salesDataProvider,
                'returnDataProvider' => $returnDataProvider,
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
