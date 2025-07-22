<?php

namespace app\controllers;

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\InvoiceGenerator;
use app\components\Utility;
use app\models\Outlet;
use app\models\ProductStatement;
use app\models\ProductStatementOutlet;
use app\models\ProductStock;
use app\models\ProductStockItemsDraft;
use app\models\ProductStockItemsOutlet;
use app\models\ProductStockItemsOutletSearch;
use app\models\ProductStockItemsSearch;
use Yii;
use app\models\ProductStockOutlet;
use app\models\ProductStockOutletSearch;
use yii\db\Exception;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ProductStockOutletController implements the CRUD actions for ProductStockOutlet model.
 */
class ProductStockOutletController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProductStockOutlet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductStockOutletSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrint($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        return InvoiceGenerator::stockOutletInvoice(Utility::decrypt($id), false);

    }

    public function actionDetails($id)
    {
        $id = Utility::decrypt($id);
        $request = Yii::$app->request;
        $searchModel = new ProductStockItemsOutletSearch();
        $searchModel->product_stock_outlet_id = $id;
        $dataProvider = $searchModel->details();
        if ($request->isAjax) {
            return $this->renderAjax('details', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('details', ['dataProvider' => $dataProvider]);

    }

    public function actionApprove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            return Yii::$app->json->error('Invalid request type.');
        }

        $encryptedId = Yii::$app->request->post('id');
        if (empty($encryptedId)) {
            return Yii::$app->json->error('Invalid request: missing ID.');

        }

        $id = Utility::decrypt($encryptedId);
        if (empty($id)) {
            return Yii::$app->json->error('Invalid ID format.');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $isCommit = true;
            $productStockOutlet = ProductStockOutlet::findOne($id);

            if (!$productStockOutlet) {
                return Yii::$app->json->error('Product stock record not found.');
            }

            if ($productStockOutlet->status !== ProductStockOutlet::STATUS_PENDING) {
                return Yii::$app->json->error('Only pending records can be approved.');
            }

            // Step 1: Update stock outlet status
            $productStockOutlet->status = ProductStockOutlet::STATUS_ACTIVE;
            $productStockOutlet->receivedBy = Yii::$app->user->id;

            if (!$productStockOutlet->save()) {
                throw new \Exception('Failed to update product stock outlet.');
            }

            // Step 2: Handle based on transfer source
            if ($productStockOutlet->transferFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                $this->handleStockTransferFromStock($productStockOutlet, $isCommit);
            } else {
                $this->handleStockTransferFromOutlet($productStockOutlet, $isCommit);
            }

            // Step 3: Insert item statement if commit is still true
            if ($isCommit) {
                $isCommit = $this->insertOutletItemStatements($productStockOutlet, $id);
            }

            if ($isCommit) {
                $transaction->commit();
                return Yii::$app->json->success('Stock transfer approved.', ['id' => $encryptedId]);
            } else {
                throw new \Exception('Commit step failed due to internal logic.');
            }

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Stock approval failed: " . $e->getMessage(), __METHOD__);
            return Yii::$app->json->error('Stock approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle stock transfer when transfer is from ProductStock.
     */
    private function handleStockTransferFromStock($productStockOutlet, &$isCommit)
    {
        $params = Json::decode($productStockOutlet->params);
        $productStock = ProductStock::findOne($productStockOutlet->ref);

        if ($productStock) {
            $productStock->created_at = DateTimeUtility::getDate($productStock->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
            $productStock->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
            $productStock->status = 'active';

            if ($productStock->save()) {
                if (empty($params['mode'])) {
                    $productStockTransfer = ProductStock::findOne($params['fromStock']);
                    if ($productStockTransfer) {
                        $productStockTransfer->created_at = DateTimeUtility::getDate($productStockTransfer->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
                        $productStockTransfer->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                        $productStockTransfer->params = '';
                        $productStockTransfer->status = 'inactive';
                        if (!$productStockTransfer->save()) {
                            $isCommit = false;
                        }
                    }
                }
            } else {
                $isCommit = false;
            }
        } else {
            $isCommit = false;
        }
    }

    /**
     * Handle stock transfer when transfer is from another outlet.
     */
    private function handleStockTransferFromOutlet($productStockOutlet, &$isCommit)
    {
        $previousOutlet = ProductStockOutlet::findOne($productStockOutlet->ref);
        if ($previousOutlet) {
            $previousOutlet->status = 'active';
            $previousOutlet->receivedBy = Yii::$app->user->id;
            if (!$previousOutlet->save()) {
                $isCommit = false;
            }
        } else {
            $isCommit = false;
        }
    }

    /**
     * Insert stock outlet item statements in bulk.
     */
    private function insertOutletItemStatements($productStockOutlet, $outletId)
    {
        $outletItems = ProductStockItemsOutlet::findAll(['product_stock_outlet_id' => $outletId]);
        $data = [];

        foreach ($outletItems as $item) {
            $data[] = [
                $productStockOutlet->receivedOutlet,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                $item->new_quantity,
                'Stock-Received',
                $productStockOutlet->remarks ?: 'Movement',
                $productStockOutlet->product_stock_outlet_id,
                Yii::$app->user->id
            ];
        }

        if (empty($data)) {
            return false;
        }

        $rowsInserted = Yii::$app->db->createCommand()->batchInsert('product_statement_outlet', [
            'outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks', 'reference_id', 'user_id'
        ], $data)->execute();

        return count($outletItems) === $rowsInserted;
    }


    public function actionReject($id)
    {
        $id = Utility::decrypt($id);
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $isCommit = true;

            /** @var ProductStockOutlet $productStockOutlet */
            $productStockOutlet = ProductStockOutlet::findOne($id);

            if ($productStockOutlet && $productStockOutlet->status === ProductStockOutlet::STATUS_PENDING) {

                // Step 1: Update current stock outlet
                $productStockOutlet->status = ProductStockOutlet::STATUS_REJECTED;
                $productStockOutlet->receivedBy = Yii::$app->user->id;

                if (!$productStockOutlet->save()) {
                    throw new \Exception('Failed to update product stock outlet status.');
                }

                // Step 2: Handle based on transferFrom type
                if ($productStockOutlet->transferFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                    $isCommit = $this->handleRejectFromStock($productStockOutlet);
                } else {
                    $isCommit = $this->handleRejectFromOutlet($productStockOutlet);
                }
            }

            if ($isCommit) {
                $transaction->commit();
                FlashMessage::setMessage("Stock Transfer Has Been Rejected", "Reject", "info");
            } else {
                throw new \Exception('Reject operation failed due to data inconsistencies.');
            }

        } catch (\Exception $exception) {
            $transaction->rollBack();
            Yii::error("Stock rejection failed: " . $exception->getMessage(), __METHOD__);
            FlashMessage::setMessage("Stock Transfer Has Been Rejected", "Reject Exception", "error");
        }

        return $this->redirect('index');
    }

    private function handleRejectFromStock($productStockOutlet)
    {
        $isCommit = true;

        $productStock = ProductStock::findOne($productStockOutlet->ref);

        if ($productStock) {
            $productStock->created_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
            $productStock->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
            $productStock->params = '';
            $productStock->status = ProductStock::STATUS_REJECT;

            if (!$productStock->save()) {
                return false;
            }

            $params = Json::decode($productStockOutlet->params);
            if (!isset($params['mode'])) {
                $productStockTransfer = ProductStock::findOne($params['fromStock'] ?? null);
                if ($productStockTransfer) {
                    $productStockTransfer->created_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                    $productStockTransfer->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                    $productStockTransfer->params = '';
                    $productStockTransfer->status = ProductStock::STATUS_ACTIVE;
                    if (!$productStockTransfer->save()) {
                        return false;
                    }
                }
            }

            ProductStockItemsOutlet::updateAll(['status' => ProductStockItemsOutlet::STATUS_REJECTED], [
                'product_stock_outlet_id' => $productStockOutlet->product_stock_outlet_id
            ]);

            $statements = ProductStatement::findAll([
                'reference_id' => $productStock->product_stock_id,
                'type' => 'Stock-Transfer'
            ]);

            foreach ($statements as $item) {
                $rejectStatement = new ProductStatement();
                $rejectStatement->item_id = $item->item_id;
                $rejectStatement->brand_id = $item->brand_id;
                $rejectStatement->size_id = $item->size_id;
                $rejectStatement->quantity = abs($item->quantity);
                $rejectStatement->type = 'Stock-Transfer-Reject';
                $rejectStatement->remarks = '';
                $rejectStatement->reference_id = $productStock->product_stock_id;
                $rejectStatement->user_id = Yii::$app->user->id;

                if (!$rejectStatement->save()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function handleRejectFromOutlet($productStockOutlet)
    {
        $isCommit = true;

        $previousOutlet = ProductStockOutlet::findOne($productStockOutlet->ref);
        if (!$previousOutlet) {
            return false;
        }

        $previousOutlet->status = ProductStockOutlet::STATUS_REJECTED;
        $previousOutlet->receivedBy = Yii::$app->user->id;

        if (!$previousOutlet->save()) {
            return false;
        }

        $outletItems = ProductStatementOutlet::findAll([
            'reference_id' => $productStockOutlet->ref,
            'type' => 'Stock-Outlet-Transfer'
        ]);

        $data = [];
        foreach ($outletItems as $item) {
            $data[] = [
                $item->outlet_id,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                abs($item->quantity),
                'Stock-Outlet-Transfer',
                'Stock-Transfer-Reject',
                $productStockOutlet->ref,
                Yii::$app->user->id
            ];
        }

        if (empty($data)) {
            return false;
        }

        $rowsInserted = Yii::$app->db->createCommand()->batchInsert('product_statement_outlet', [
            'outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks', 'reference_id', 'user_id'
        ], $data)->execute();

        return count($outletItems) === $rowsInserted;
    }

    /**
     * Finds the ProductStockOutlet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductStockOutlet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductStockOutlet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
