<?php

namespace app\controllers;

use app\components\API;
use app\components\SystemSettings;
use app\components\DateTimeUtility;

use app\components\FlashMessage;
use app\components\ProductOutletUtility;
use app\components\ProductUtility;
use app\components\Utility;
use app\models\Outlet;
use app\models\ProductItemsPrice;
use app\models\ProductStatement;
use app\models\ProductStatementOutlet;
use app\models\ProductStockItems;
use app\models\ProductStockItemsDraft;
use app\models\ProductStockItemsDraftSearch;

use app\models\ProductStockItemsOutlet;
use app\models\ProductStockItemsSearch;
use app\models\ProductStockOutlet;
use app\models\ProductStockOutletSearch;
use Yii;
use app\models\ProductStock;
use app\models\ProductStockSearch;

use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ProductStockController implements the CRUD actions for ProductStock model.
 */
class ProductStockMovementController extends Controller
{

    public function actionGetItemByBrand()
    {
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $brandId = $parents[0];
                $items = ProductUtility::getItemListByBrand($brandId);
                foreach ($items as $brand) {
                    $out[] = ['id' => $brand->item_id, 'name' => $brand->item_name];
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetBrandListByItem()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $itemId = $parents[0];
                $brands = ProductUtility::getBrandListByItem($itemId);
                foreach ($brands as $brand) {
                    $out[] = ['id' => $brand->brand_id, 'name' => $brand->brand_name];
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetSizeListByBrand()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $itemId = $parents[0];
                $brandId = $parents[1];
                $sizes = ProductUtility::getSizeListByBrand($itemId, $brandId);
                foreach ($sizes as $size) {
                    $out[] = ['id' => $size->size_id, 'name' => $size->size_name];
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionProductDetailsBySizeId($sizeId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = ProductItemsPrice::find()->where(['size_id' => $sizeId])->one();
        if ($model) {
            return [
                'error' => false,
                'costPrice' => number_format($model->cost_price),
                'wholesalePrice' => number_format($model->wholesale_price),
                'retailPrice' => number_format($model->retail_price),
                'alert' => number_format($model->alert_quantity),
            ];
        }
        return [
            'error' => false,
            'costPrice' => 0,
            'wholesalePrice' => 0,
            'retailPrice' => 0,
            'alert' => 0,
        ];
    }

    /**
     * @param ProductStockItemsDraft $model
     * @param array $data
     * @return array
     */
    private function addItemDraft(ProductStockItemsDraft $model, array $data, $souce = ProductStockItemsDraft::SOURCE_MOVEMENT)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model->load($data);
        $model->source = $souce;
        if ($model->save()) {
            return ['error' => false, 'message' => 'success'];
        }

        return ['error' => true, 'message' => ActiveForm::validate($model)];
    }

    public function actionGetProductPrice()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            if (!empty($request['sizeId']) && !empty($request['transferOutlet'])) {
                return ProductOutletUtility::getPriceWthQuantityBySize($request['sizeId'], Utility::decrypt($request['transferOutlet']));
            }
        }
    }

    public function actionOutlet()
    {
        $model = new ProductStockOutlet();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            return $this->redirect(['transfer-create', 'outlet' => Utility::encrypt($model->transferOutlet)]);
        }
        return $this->render('_outlet', [
            'model' => $model
        ]);
    }

    private function stockTransferSave(ProductStockOutlet $model)
    {

        $errors = [];
        $productStockItemsDraft = ProductStockItemsDraft::find()->where([
                'user_id' => yii::$app->user->getid(),
                'source' => productstockitemsdraft::SOURCE_MOVEMENT,
                'type' => productstockitemsdraft::TYPE_INSERT])
            ->all();

        $isSave = true;
        $productStockPk = 0;
        $outletItemsFrom = [];
        $outletStatement = [];
        $outletItemsTo = [];

        if ($model->receivedFrom === ProductStockOutlet::TRANSFER_FROM_OUTLET) {

            $lastRecord = 1;
            $record = ProductStockOutlet::find()->orderBy('product_stock_outlet_id DESC')->one();
            if ($record) {
                $lastRecord += $record->product_stock_outlet_id;
            }

            $productStockOutlet = new ProductStockOutlet();
            $productStockOutlet->ref = $model->product_stock_outlet_id;
            $productStockOutlet->transferFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
            $productStockOutlet->receivedFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
            $productStockOutlet->invoice = Utility::genInvoice($lastRecord, 'STOR', 8);
            $productStockOutlet->product_stock_outlet_code = uniqid(rand(1, time()));
            $productStockOutlet->type = ProductStockOutlet::TYPE_RECEIVED;
            $productStockOutlet->receivedOutlet = $model->receivedOutlet;
            $productStockOutlet->transferOutlet = $model->transferOutlet;
            $productStockOutlet->transferBy = Yii::$app->user->id;
            $productStockOutlet->status = ProductStockOutlet::STATUS_PENDING;
            if ($productStockOutlet->save()) {
                $productStockPk = $productStockOutlet->product_stock_outlet_id;
            } else {
                $isSave = false;
                $errors = $productStockOutlet->getErrors();
            }
        } else {
            $lastRecord = 1;
            $record = ProductStock::find()->orderBy('product_stock_id DESC')->one();
            if ($record) {
                $lastRecord += $record->product_stock_id;
            }

            $outlet = Outlet::findOne($model->transferOutlet);

            $productStock = new ProductStock();
            $productStock->type = ProductStock::TYPE_RECEIVED;
            $productStock->invoice_no = Utility::genInvoice($lastRecord, 'STOR-');
            $productStock->setScenario('stock');
            $productStock->user_id = Yii::$app->user->id;
            $productStock->params = Json::encode(['transferOutlet' => $outlet->name, 'ref' => $model->product_stock_outlet_id]);
            $productStock->status = ProductStock::STATUS_PENDING;
            if ($productStock->save()) {
                $productStockPk = $productStock->product_stock_id;
            } else {
                $isSave = false;
                $errors = $productStock->getErrors();
            }
        }


        foreach ($productStockItemsDraft as $item) {

            $prvQty = ProductOutletUtility::getTotalQuantity($item->size_id, $model->transferOutlet);

            $outletItemsFrom[] = [
                $model->product_stock_outlet_id,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                $item->cost_price,
                $item->wholesale_price,
                $item->retail_price,
                $prvQty,
                $item->new_quantity,
                $prvQty - $item->new_quantity,
                $model->transferOutlet,
                $model->receivedOutlet,
                ProductStockItemsOutlet::STATUS_PENDING
            ];

            $outletStatement[] = [
                $model->transferOutlet,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                -$item->new_quantity,
                'Stock-Outlet-Transfer',
                'Transfer',
                $model->product_stock_outlet_id,
                Yii::$app->user->id,
                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'),
                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka')
            ];

            if ($model->receivedFrom === ProductStockOutlet::TRANSFER_FROM_OUTLET) {

                $prvQty = ProductOutletUtility::getTotalQuantity($item->size_id, $model->receivedOutlet);

                $outletItemsTo[] = [
                    $productStockPk,
                    $item->item_id,
                    $item->brand_id,
                    $item->size_id,
                    $item->cost_price,
                    $item->wholesale_price,
                    $item->retail_price,
                    $prvQty,
                    $item->new_quantity,
                    $prvQty + $item->new_quantity,
                    $model->transferOutlet,
                    $model->receivedOutlet,
                    ProductStockItemsOutlet::STATUS_DONE
                ];

            } else {

                $prvQty = ProductUtility::getTotalQuantity($item->size_id);

                $outletItemsTo[] = [
                    $productStockPk,
                    $item->item_id,
                    $item->brand_id,
                    $item->size_id,
                    $item->cost_price,
                    $item->wholesale_price,
                    $item->retail_price,
                    $prvQty,
                    $item->new_quantity,
                    $prvQty + $item->new_quantity,
                    ProductStockItemsOutlet::STATUS_DONE
                ];

            }

        }


        if (count($outletItemsFrom) > 0 && count($outletStatement) > 0 && $isSave) {

            $totalRecord = Yii::$app->db->createCommand()->batchInsert(ProductStockItemsOutlet::tableName(), [
                'product_stock_outlet_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price',
                'previous_quantity', 'new_quantity', 'total_quantity', 'transferOutlet', 'receivedOutlet', 'status'],
                $outletItemsFrom
            )->execute();

            if (count($outletItemsFrom) === $totalRecord) {

                $totalRecord = Yii::$app->db->createCommand()->batchInsert(ProductStatementOutlet::tableName(), [
                    'outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type',
                    'remarks', 'reference_id', 'user_id', 'created_at', 'updated_at'],
                    $outletStatement
                )->execute();

                if (count($outletStatement) === $totalRecord) {

                    if ($model->receivedFrom === ProductStockOutlet::TRANSFER_FROM_OUTLET) {

                        $totalRecord = Yii::$app->db->createCommand()->batchInsert(ProductStockItemsOutlet::tableName(), [
                            'product_stock_outlet_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price',
                            'previous_quantity', 'new_quantity', 'total_quantity', 'transferOutlet', 'receivedOutlet', 'status'],
                            $outletItemsTo
                        )->execute();

                        if (count($outletItemsTo) === $totalRecord) {
                            return true;
                        }

                    } else {

                        $totalRecord = Yii::$app->db->createCommand()->batchInsert(ProductStockItems::tableName(), [
                            'product_stock_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price',
                            'previous_quantity', 'new_quantity', 'total_quantity', 'status'],
                            $outletItemsTo
                        )->execute();

                        if (count($outletItemsTo) === $totalRecord) {
                            return true;
                        }
                    }
                }
            }
        }


        return false;
    }

    public function actionTransferCreate($outlet)
    {

        $outlet = Utility::decrypt($outlet);
        if (empty($outlet) && !is_numeric($outlet)) {
            FlashMessage::setMessage('Select Outlet', 'Outlet', 'error');
            return $this->redirect(['outlet']);
        }

        $lastRecord = 1;
        $record = ProductStockOutlet::find()->orderBy('product_stock_outlet_id DESC')->one();
        if ($record) {
            $lastRecord += $record->product_stock_outlet_id;
        }

        $userId = Yii::$app->user->getId();
        $productStockOutlet = new ProductStockOutlet();
        $productStockOutlet->transferFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
        $productStockOutlet->transferOutlet = $outlet;
        $productStockOutlet->receivedFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
        $productStockOutlet->invoice = Utility::genInvoice($lastRecord, 'STOT', 8);
        $productStockOutlet->product_stock_outlet_code = uniqid(rand(1, time()));
        $productStockOutlet->type = ProductStockOutlet::TYPE_TRANSFER;
        $productStockOutlet->transferBy = $userId;
        $productStockOutlet->status = ProductStockOutlet::STATUS_PENDING;

        $model = new ProductStockItemsDraft();
        $model->setScenario('stockDraft');
        $model->outletId = $outlet;
        $model->cost_price = 1;
        $model->wholesale_price = 1;
        $model->retail_price = 1;
        $model->getTotalQuantity();
        $model->type = ProductStockItemsDraft::TYPE_INSERT;
        $model->user_id = $userId;

        $searchModel = new ProductStockItemsDraftSearch();
        $searchModel->type = ProductStockItemsDraft::TYPE_INSERT;
        $searchModel->source = ProductStockItemsDraft::SOURCE_MOVEMENT;
        $dataProvider = $searchModel->searchByType();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (Yii::$app->request->post('ProductStockItemsDraft')) {
                //if (isset($data['ProductStockItemsDraft'])) {
                $model->load(Yii::$app->request->post());
                $existItems = ProductStockItemsDraft::find()->where(['size_id' => $model, 'outletId' => $model->outletId])->one();
                if ($existItems) {
                    $existItems->new_quantity += $model->new_quantity;
                    $priceQuantity = ProductOutletUtility::getPriceWthQuantityBySize($model->size_id, $outlet);
                    $availableQty = $priceQuantity['quantity'];
                    if ($existItems->new_quantity > $priceQuantity['quantity']) {
                        $data = ['error' => true, 'message' => ["Quantity should be less than available {$availableQty}"]];
                    } else {
                        if ($existItems->save()) {
                            $data = ['error' => false, 'message' => 'success'];
                        } else {
                            $data = ['error' => true, 'message' => ActiveForm::validate($model)];
                        }
                    }
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $data;
                }

                $model->source = ProductStockItemsDraft::SOURCE_MOVEMENT;
                $priceQuantity = ProductOutletUtility::getPriceWthQuantityBySize($model->size_id, $outlet);
                $availableQty = $priceQuantity['quantity'];
                if ($model->new_quantity > $priceQuantity['quantity']) {
                    $data = ['error' => true, 'message' => ["Quantity should be less than available {$availableQty}"]];
                } else {
                    if ($model->save()) {
                        $data = ['error' => false, 'message' => 'success'];
                    } else {
                        $data = ['error' => true, 'message' => ActiveForm::validate($model)];
                    }
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $data;
            } else {

                $productStockOutlet->load(Yii::$app->request->post());

                if ($productStockOutlet->receivedOutlet == 0) {
                    $productStockOutlet->receivedOutlet = -1;
                    $productStockOutlet->receivedFrom = ProductStockOutlet::TRANSFER_FROM_STOCK;
                }

                if (empty($productStockOutlet->transferOutlet)) {
                    $productStockOutlet->addError('transferOutlet', 'Please select Outlet');
                }

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if ($productStockOutlet->save()) {

                        if ($this->stockTransferSave($productStockOutlet)) {

                            $delete = ProductStockItemsDraft::deleteAll([
                                'user_id' => Yii::$app->user->id,
                                'outletId' => $outlet,
                                'type' => ProductStockItemsDraft::TYPE_INSERT,
                                'source' => ProductStockItemsDraft::SOURCE_MOVEMENT,
                            ]);

                            $transaction->commit();
                            FlashMessage::setMessage("Stock transfer product#" . $productStockOutlet->invoice . " has been added & Move.", "Stock Movement", "success");
                        } else {
                            $transaction->rollBack();
                            FlashMessage::setMessage("Stock transfer product#" . $productStockOutlet->invoice . " has been added but unable to Move.", "Stock Movement", "success");

                        }
                        return $this->redirect(['/product-stock-outlet/index']);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    FlashMessage::setMessage("Stock transfer unable to process", "Stock Movement Exception", "error");
                    print_r($e);
                }
            }
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('transfer/create', [
                'model' => $model,
                'productStock' => $productStockOutlet,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('transfer/create', [
            'model' => $model,
            'productStock' => $productStockOutlet,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function deleteDraft($type, $source)
    {
        return ProductStockItemsDraft::deleteAll(['user_id' => Yii::$app->user->getId(), 'type' => $type, 'source' => $source]);
    }

    public function actionTransferReject($id)
    {
        $model = ProductStock::findOne(Utility::decrypt($id));
        $model->created_at = DateTimeUtility::getDate($model->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
        $model->status = ProductStock::STATUS_REJECT;
        $response = API::stockTransferReject($model);
        if ($response->success) {
            if ($model->save()) {
                $sql = "UPDATE `product_stock_items` SET `status`='done' WHERE product_stock_id='{$model->product_stock_id}'";
                if (Yii::$app->db->createCommand($sql)->execute()) {
                    FlashMessage::setMessage("Stock transfer product# {$model->product_stock_id} has been rejected.", "Stock Movement", "success");
                    return true;
                } else {
                    FlashMessage::setMessage("Stock transfer product# {$model->product_stock_id} has not been rejected.", "Stock Movement", "error");
                    return false;
                }
            }
        }
    }

    public function actionTransferApproved()
    {
        if (Yii::$app->request->isGet) {
            $id = Yii::$app->request->get('id');
            $model = ProductStock::findOne(Utility::decrypt($id));
            $data = ProductUtility::stockMovementApproved($model);
            if ($data['success']) {
                $response = API::stockTransferApproved($model);
                if ($response->success) {
                    FlashMessage::setMessage("Stock transfer product# {$model->product_stock_id} has been Approved.", "Stock Movement", "success");
                    return true;
                }
            } else {
                FlashMessage::setMessage("Stock transfer product# {$model->product_stock_id} hasn't Move successfully.", "Stock Movement", "error");
                return false;
            }
        }
    }

    public function actionTransfer()
    {
        //$this->deleteDraft(ProductStockItemsDraft::TYPE_UPDATE, ProductStockItemsDraft::SOURCE_MOVEMENT);
        $searchModel = new ProductStockOutletSearch();
        $dataProvider = $searchModel->movement(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the ProductStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductStock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
