<?php

namespace app\controllers;

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\PdfGen;
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

        return PdfGen::stockOutletInvoice(Utility::decrypt($id), false);

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

    public function actionApprove($id)
    {
        /* @property ProductStockItemsOutlet $outletItem */

        $id = Utility::decrypt($id);
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $isCommit = true;

            $productStockOutlet = ProductStockOutlet::findOne($id);
            if ($productStockOutlet->status === 'pending') {
                $productStockOutlet->status = ProductStockOutlet::STATUS_ACTIVE;
                $productStockOutlet->receivedBy = Yii::$app->user->id;
                if ($productStockOutlet->save()) {
                    if ($productStockOutlet->transferFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                        $params = Json::decode($productStockOutlet->params);
                        $productStock = ProductStock::findOne($productStockOutlet->ref);
                        $productStock->created_at = DateTimeUtility::getDate($productStock->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
                        $productStock->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                        $productStock->status = 'active';
                        if ($productStock->save()) {
                            if (!isset($params['mode'])) {
                                $productStockTransfer = ProductStock::findOne($params['coreStock']);
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

                        $previousProductStockOutlet = ProductStockOutlet::findOne($productStockOutlet->ref);
                        $previousProductStockOutlet->status = 'active';
                        $previousProductStockOutlet->receivedBy = Yii::$app->user->id;
                        if (!$previousProductStockOutlet->save()) {
                            $isCommit = false;
                        }
                    }

                    if ($isCommit) {

                        $data = [];
                        $outletItems = ProductStockItemsOutlet::findAll(['product_stock_outlet_id' => $id]);

                        foreach ($outletItems as $outletItem) {
                            $data[] = [
                                $productStockOutlet->receivedOutlet,
                                $outletItem->item_id,
                                $outletItem->brand_id,
                                $outletItem->size_id,
                                $outletItem->new_quantity,
                                'Stock-Received',
                                $productStockOutlet->remarks ? $productStockOutlet->remarks : 'Movement',
                                $productStockOutlet->product_stock_outlet_id,
                                Yii::$app->user->id
                            ];
                        }

                        $totalBulkInsert = Yii::$app->db->createCommand()->batchInsert('product_statement_outlet',
                            ['outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks', 'reference_id', 'user_id'],
                            $data
                        )->execute();

                        if (count($outletItems) === $totalBulkInsert) {
                            $isCommit = true;
                        } else {
                            $isCommit = false;
                        }
                    }
                }
            }

            if ($isCommit) {
                $transaction->commit();
                return $this->redirect('index');
            }

        } catch (\Exception $e) {
            dd($e);
            $transaction->rollBack();
        }
    }

    public function actionReject($id)
    {
        $id = Utility::decrypt($id);

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $isCommit = true;

            $productStockOutlet = ProductStockOutlet::findOne($id);

            if ($productStockOutlet->status === 'pending') {
                $productStockOutlet->status = ProductStockOutlet::STATUS_REJECTED;
                $productStockOutlet->receivedBy = Yii::$app->user->id;

                if ($productStockOutlet->save()) {

                    if ($productStockOutlet->transferFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                        $productStock = ProductStock::findOne($productStockOutlet->ref);
                        $productStock->created_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                        $productStock->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                        $productStock->params = '';
                        $productStock->status = 'reject';
                        if ($productStock->save()) {

                            $params = Json::decode($productStockOutlet->params);

                            if (!isset($params['mode'])) {
                                $productStockTransfer = ProductStock::findOne($params['coreStock']);
                                $productStockTransfer->created_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                                $productStockTransfer->updated_at = DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka');
                                $productStockTransfer->params = '';
                                $productStockTransfer->status = 'active';
                                if (!$productStockTransfer->save()) {
                                    $isCommit = false;
                                }
                            }

                            ProductStockItemsOutlet::updateAll(['status' => ProductStockItemsOutlet::STATUS_REJECTED], ['product_stock_outlet_id' => $id]);
                            $statement = ProductStatement::findAll(['reference_id' => $productStock->product_stock_id, 'type' => 'Stock-Transfer']);
                            foreach ($statement as $item) {
                                $outletStatement = new ProductStatement();
                                $outletStatement->item_id = $item->item_id;
                                $outletStatement->brand_id = $item->brand_id;
                                $outletStatement->size_id = $item->size_id;
                                $outletStatement->quantity = abs($item->quantity);
                                $outletStatement->type = 'Stock-Transfer-Reject';
                                $outletStatement->remarks = '';
                                $outletStatement->reference_id = $productStock->product_stock_id;
                                $outletStatement->user_id = Yii::$app->user->id;
                                if (!$outletStatement->save()) {
                                    $isCommit = false;
                                }
                            }

                        }
                    } else {

                        $previousProductStockOutlet = ProductStockOutlet::findOne($productStockOutlet->ref);
                        $previousProductStockOutlet->status = 'reject';
                        $previousProductStockOutlet->receivedBy = Yii::$app->user->id;
                        if ($previousProductStockOutlet->save()) {

                            $data = [];
                            $outletItems = ProductStatementOutlet::findAll(['reference_id' => $productStockOutlet->ref, 'type' => 'Stock-Outlet-Transfer']);
                            foreach ($outletItems as $outletItem) {
                                $data[] = [
                                    $outletItem->outlet_id,
                                    $outletItem->item_id,
                                    $outletItem->brand_id,
                                    $outletItem->size_id,
                                    abs($outletItem->quantity),
                                    'Stock-Outlet-Transfer',
                                    'Stock-Transfer-Reject',
                                    $productStockOutlet->ref,
                                    Yii::$app->user->id
                                ];
                            }

                            $totalBulkInsert = Yii::$app->db->createCommand()->batchInsert('product_statement_outlet',
                                ['outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks', 'reference_id', 'user_id'],
                                $data
                            )->execute();

                            if (count($outletItems) === $totalBulkInsert) {
                                $isCommit = true;
                            } else {
                                $isCommit = false;
                            }

                        }
                    }
                }

            }

            if ($isCommit) {
                $transaction->commit();
                FlashMessage::setMessage("Stock Transfer Has Been Rejected", "Reject", "info");
            } else {
                FlashMessage::setMessage("Stock Transfer Has Been Rejected", "Reject", "error");
                $transaction->rollBack();
            }

        } catch (Exception $exception) {
            $transaction->rollBack();
            FlashMessage::setMessage("Stock Transfer Has Been Rejected", "Reject Exception", "error");
        }

        return $this->redirect('index');
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
