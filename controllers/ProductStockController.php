<?php

namespace app\controllers;

use app\components\API;
use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\components\DBUtility;
use app\components\FlashMessage;
use app\components\PdfGen;
use app\components\ProductUtility;
use app\components\TransactionStore;
use app\components\Utility;
use app\models\AppSettings;
use app\models\City;
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
use app\models\Size;
use app\modules\asm\components\ASM;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Monolog\Utils;
use Yii;
use app\models\ProductStock;
use app\models\ProductStockSearch;
use yii\caching\TagDependency;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;

/**
 * ProductStockController implements the CRUD actions for ProductStock model.
 */
class ProductStockController extends Controller
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

    public function actionGetProductPrice()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            if (isset($request['sizeId'])) {
                return ProductUtility::getPriceWthQuantityBySize($request['sizeId']);
            }
        }
    }

    public function actionExistingPrice($sizeId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isGet && !empty($sizeId)) {
            $cacheKey = "existingPrice:size:{$sizeId}";

            $model = Yii::$app->cache->getOrSet($cacheKey, function () use ($sizeId) {
                return ProductItemsPrice::find()->where(['size_id' => $sizeId])->one();
            }, 3600, new TagDependency(['tags' => "productStockPrice:size:{$sizeId}"]));

            if ($model) {
                return [
                    'success' => true,
                    'cost' => $model->cost_price,
                    'wholesale' => $model->wholesale_price,
                    'retail' => $model->retail_price,
                    'alert' => $model->alert_quantity,
                ];
            }
        }

        return [
            'success' => false,
            'cost' => '',
            'wholesale' => '',
            'retail' => '',
            'alert' => '',
        ];
    }

    /**
     * @param ProductStockItemsDraft $model
     * @param array $data
     * @return array
     */
    private function addItemDraft(ProductStockItemsDraft $model, $data = [], $source = ProductStockItemsDraft::SOURCE_MOVEMENT)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model->load($data);
        $model->source = $source;
        if ($model->save()) {
            return ['error' => false, 'message' => 'success'];
        }
        return ['error' => true, 'message' => ActiveForm::validate($model)];
    }

    /**
     * Creates a new ProductStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param ProductStock $model
     * @return mixed
     */

    /**
     * Creates a new ProductStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $userId = Yii::$app->user->getId();
        $productStock = new ProductStock();
        $productStock->invoice_no = Utility::genInvoice('STI-');
        $productStock->setScenario('stock');
        $productStock->user_id = $userId;

        $model = new ProductStockItemsDraft();
        $model->setScenario('stockDraft');
        $model->getTotalQuantity();
        $model->type = ProductStockItemsDraft::TYPE_INSERT;
        $model->user_id = $userId;

        $searchModel = new ProductStockItemsDraftSearch();
        $searchModel->type = ProductStockItemsDraft::TYPE_INSERT;
        $searchModel->source = ProductStockItemsDraft::SOURCE_STOCK;
        $dataProvider = $searchModel->searchByType();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (isset($data['ProductStockItemsDraft'])) {
                return $this->addItemDraft($model, $data, ProductStockItemsDraft::SOURCE_STOCK);
            } else {
                $data = Yii::$app->request->post('ProductStock');
                $productStock->setAttributes($data);
                if ($productStock->type == ProductStock::TYPE_IMPORT) {
                    if (empty($productStock->lc_id)) {
                        $productStock->addError('lc_id', 'Please select LC');
                    }
                    if (empty($productStock->warehouse_id)) {
                        $productStock->addError('warehouse_id', 'Please select Warehouse');
                    }
                } elseif ($productStock->type == ProductStock::TYPE_LOCAL) {
                    if (empty($productStock->buyer_id)) {
                        $productStock->addError('buyer_id', 'Please select Supplier');
                    }
                }

                $productStock->load($data);
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($productStock->save()) {
                        $productStockItemsDraft = ProductStockItemsDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'type' => ProductStockItemsDraft::TYPE_INSERT, 'source' => ProductStockItemsDraft::SOURCE_STOCK])->all();
                        if(count($productStockItemsDraft) > 0){
                            if (ProductStock::saveToInventory($productStock, $productStockItemsDraft)) {
                                ProductStock::stockDraftRemove(ProductStockItemsDraft::TYPE_INSERT);
                                $transaction->commit();
                                FlashMessage::setMessage("New Stock #" . $productStock->invoice_no . " has been added.", "New Stock", "success");
                                return $this->redirect(['index']);
                            }
                        }else{
                            FlashMessage::setMessage("No items found in the stock card.", "Stock Cart Empty", "warning");
                        }

                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('stock/create', [
            'model' => $model,
            'productStock' => $productStock,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrint($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        echo "<pre>";

        try {
            return PdfGen::stockInvoice(Utility::decrypt($id), false);
        } catch (\yii\base\Exception $exception) {
            dd($exception->getMessage());
            die();
        }

    }

    public function actionDetails($id)
    {
        $model = ProductStock::findOne(Utility::decrypt($id));

        $searchModel = new ProductStockItemsSearch();
        $searchModel->product_stock_id = $model->product_stock_id;
        $dataProvider = $searchModel->details(Yii::$app->request->queryParams);

        return $this->renderAjax('details', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionItemsDetails($id)
    {
        $searchModel = new ProductStockItemsSearch();
        $searchModel->product_stock_id = Utility::decrypt($id);
        $dataProvider = $searchModel->details(Yii::$app->request->queryParams);

        return $this->renderAjax('items-details', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionItems()
    {
        $searchModel = new ProductStockItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);

        return $this->render('items\items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing ProductStockItemsDraft model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws Exception
     */
    public function actionStockUpdate($id)
    {

        $this->itemMoveToDraftTable($id);

        $productStock = ProductStock::findOne($id);
        $productStock->user_id = Yii::$app->user->getId();

        $model = new ProductStockItemsDraft();
        $model->getTotalQuantity();

        $model->user_id = Yii::$app->user->getId();
        $model->type = ProductStockItemsDraft::TYPE_UPDATE;
        $model->product_stock_id = $id;
        $searchModel = new ProductStockItemsDraftSearch();
        $searchModel->type = $model->type;
        $searchModel->product_stock_id = $id;
        $dataProvider = $searchModel->searchByType();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (isset($data['ProductStockItemsDraft'])) {
                return $this->addItemDraft($model, $data, ProductStockItemsDraft::SOURCE_STOCK);
            } else {

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if ($productStock->save()) {

                        // Delete existing stock items
                        ProductStockItems::deleteAll([
                            'product_stock_id' => $productStock->product_stock_id
                        ]);

                        // Delete existing product statements of type STOCK
                        ProductStatement::deleteAll([
                            'reference_id' => $productStock->product_stock_id,
                            'type' => ProductStatement::TYPE_STOCK
                        ]);

                        // Get updated draft items
                        $productStockItemsDraft = ProductStockItemsDraft::find()->where([
                            'user_id' => Yii::$app->user->getId(),
                            'type' => ProductStockItemsDraft::TYPE_UPDATE,
                            'source' => ProductStockItemsDraft::SOURCE_STOCK
                        ])->all();


                        if(count($productStockItemsDraft)> 0){
                            // Save to inventory and commit
                            if (ProductStock::saveToInventory($productStock, $productStockItemsDraft)) {
                                ProductStock::stockDraftRemove(ProductStockItemsDraft::TYPE_UPDATE);
                                $transaction->commit();
                                FlashMessage::setMessage(
                                    "New Stock #" . $productStock->invoice_no . " has been added.",
                                    "New Stock",
                                    "success"
                                );
                                return $this->redirect(['index']);
                            }
                        }else{
                            FlashMessage::setMessage("No items found in the stock card.", "Stock Cart Empty", "warning");
                            return $this->redirect(['index']);
                        }

                        // Roll back if inventory save failed
                        $transaction->rollBack();
                    } else {
                        // Optional: Add model error flash/log if save() failed
                        $transaction->rollBack();
                    }
                } catch (\Throwable $e) {
                    Yii::error("Stock save failed: " . $e->getMessage(), __METHOD__);
                    $transaction->rollBack();
                }
            }
        }


        return $this->render('stock/update', [
            'model' => $model,
            'productStock' => $productStock,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionTransferToStore($id)
    {
        $id = Utility::decrypt($id);
        $this->itemMoveToDraftTable($id, ProductStockItemsDraft::SOURCE_MOVEMENT);


        $oldProductStock = ProductStock::findOne($id);

        $lastId = 1;
        $stockRecord = ProductStock::find()->orderBy('product_stock_id DESC')->one();
        if ($stockRecord) {
            $lastId = $stockRecord->product_stock_id;
        }

        $productStock = new ProductStock();
        $productStock->setScenario('transfer');
        $productStock->type = ProductStock::TYPE_TRANSFER;
        $productStock->invoice_no = Utility::genInvoice('STO-');
        $productStock->status = ProductStock::STATUS_PENDING;
        $productStock->user_id = Yii::$app->user->getId();

        $model = new ProductStockItemsDraft();
        $model->user_id = Yii::$app->user->getId();
        $model->type = 'insert';
        $model->product_stock_id = $id;

        $searchModel = new ProductStockItemsDraftSearch();
        $searchModel->type = $model->type = 'update';
        $searchModel->product_stock_id = $id;
        $dataProvider = $searchModel->searchByType();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (isset($data['ProductStockItemsDraft'])) {
                return $this->addItemDraft($model, $data);
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $items = ProductStockItemsDraft::findAll(['product_stock_id' => $id]);
                $productStock->load($data);
                $outlet = Outlet::findOne($productStock->outlet);
                $productStock->params = Json::encode(['receivedOutlet' => $outlet->name, 'fromStock' => $id]);

                try {
                    if ($productStock->save()) {

                        // Save product stock items to the target stock
                        $productStockItemsSaved = ProductStockItemsOutlet::draftToStockItems($productStock->product_stock_id, $items);

                        // Save outlet stock details
                        $productStockOutletSaved = ProductStockOutlet::saveOutletStock($id, $productStock, $data, $items);

                        // Prepare stock transfer parameters
                        $params = ['Type' => ProductStock::TYPE_TRANSFER];
                        // Update the source stock record with transfer details
                        $updatedRows = ProductStock::updateAll(
                            ['params' => Json::encode($params)],
                            ['product_stock_id' => $id]
                        );

                        // All operations must succeed
                        if ($productStockItemsSaved && $productStockOutletSaved && $updatedRows > 0) {
                            $transaction->commit();

                            $message = "Stock Transfer# " . $productStock->invoice_no . " has been created.";
                            FlashMessage::setMessage($message, "Stock Transfer To Store", "info");

                            return $this->redirect(['index']);
                        } else {
                            // If any operation failed, rollback the transaction
                            $transaction->rollBack();

                            Yii::error("Stock transfer failed: ItemsSaved: {$productStockItemsSaved}, OutletSaved: {$productStockOutletSaved}, RowsUpdated: {$updatedRows}", __METHOD__);
                            FlashMessage::setMessage("Stock transfer failed due to incomplete operations.", "Stock Transfer Error", "error");
                        }

                    } else {
                        // Rollback if saving the main ProductStock record failed
                        $transaction->rollBack();

                        Yii::error("Product stock save failed: " . Json::encode($productStock->getErrors()), __METHOD__);
                        FlashMessage::setMessage("Failed to save product stock: " . implode(', ', array_map(function($v) { return implode(' ', $v); }, $productStock->getErrors())), "Stock Transfer Error", "error");
                    }

                } catch (\Exception $exception) {
                    $transaction->rollBack();

                    Yii::error("Exception during stock transfer: " . $exception->getMessage(), __METHOD__);
                    FlashMessage::setMessage("An unexpected error occurred: " . $exception->getMessage(), "Stock Transfer Error", "error");
                }

            }
        }


        return $this->render('transfer-to-store/create', [
            'model' => $model,
            'productStock' => $productStock,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }
    /**
     * Lists all ProductStock models.
     * @return mixed
     */
    public function actionIndex()
    {
        //$this->deleteDraft(ProductStockItemsDraft::TYPE_UPDATE, ProductStockItemsDraft::SOURCE_STOCK);
        $searchModel = new ProductStockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array
     */
    public function actionStockDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        if (!isset($data['id']) || empty($data['id'])) {
            return [
                'error' => true,
                'message' => 'Invalid ID provided.'
            ];
        }

        $deleted = ProductStockItemsDraft::deleteAll(['product_stock_items_draft_id' => $data['id']]);

        if ($deleted) {
            return [
                'error' => false,
                'message' => 'Item successfully deleted.'
            ];
        }

        return [
            'error' => true,
            'message' => 'Item could not be deleted or does not exist.'
        ];
    }

    public function actionTransfer()
    {

        $lastId = 1;
        $stockRecord = ProductStock::find()->orderBy('product_stock_id DESC')->one();
        if ($stockRecord) {
            $lastId = $stockRecord->product_stock_id;
        }

        $productStock = new ProductStock();
        $productStock->setScenario('transfer');
        $productStock->type = ProductStock::TYPE_TRANSFER;
        $productStock->invoice_no = Utility::genInvoice($lastId, 'STO-');
        $productStock->status = ProductStock::STATUS_PENDING;
        $productStock->user_id = Yii::$app->user->getId();

        $model = new ProductStockItemsDraft();
        $model->user_id = Yii::$app->user->getId();
        $searchModel = new ProductStockItemsDraftSearch();
        $searchModel->type = ProductStockItemsDraft::TYPE_INSERT;
        $dataProvider = $searchModel->searchByType();

        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('ProductStockItemsDraft')) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $data = Yii::$app->request->post();
                $sizeId = $data['ProductStockItemsDraft']['size_id'];
                $qty = (int) (ProductUtility::getTotalQuantity($sizeId) - ProductUtility::getDraftProductQuantity($sizeId));
                if ($qty > 0) {
                    if ((int) $data['ProductStockItemsDraft']['new_quantity'] <= $qty) {
                        $data['ProductStockItemsDraft']['type'] = ProductStockItemsDraft::TYPE_INSERT;
                        return $this->addItemDraft($model, $data, ProductStockItemsDraft::SOURCE_TRANSFER);
                    } else {
                        return [
                            'error' => true,
                            'message' => ["Transfer quantity cannot be greater than available stock quantity (Available: {$qty})."],
                        ];
                    }
                } else {
                    return [
                        'error' => true,
                        'message' => ["Sorry! There is no available stock for the selected item and size."],
                    ];
                }
            } else {
                $data = Yii::$app->request->post();
                $transaction = Yii::$app->db->beginTransaction();
                $items = ProductStockItemsDraft::findAll(['source' => ProductStockItemsDraft::SOURCE_TRANSFER, 'user_id' => Yii::$app->user->getId()]);
                $productStock->load($data);
                $outlet = Outlet::findOne($productStock->outlet);
                $productStock->params = Json::encode([]);

                try {
                    if ($productStock->save()) {
                        $isSaveStockItems = ProductStock::draftToStockItems($productStock->product_stock_id, $items);
                        $isSaveStockOutlet = ProductStock::saveToStoreInventory($productStock, $data, $items, $outlet);
                        if ($isSaveStockItems && $isSaveStockOutlet) {
                            $transaction->commit();
                            $message = "Stock Transfer# " . $productStock->invoice_no . "has been created.";
                            FlashMessage::setMessage($message, "Stock Transfer To Store", "info");
                            return $this->redirect(['index']);
                        }
                    }
                } catch (\Exception $exception) {
                    $transaction->rollBack();
                }
            }
        }

        if(Yii::$app->request->isAjax) {
            return $this->renderAjax('transfer/create', [
                'model' => $model,
                'productStock' => $productStock,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('transfer/create', [
            'model' => $model,
            'productStock' => $productStock,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionReceivedReject($id)
    {
        $id = Utility::decrypt($id);
        $productStock = ProductStock::findOne($id);
        $productStock->user_id = Yii::$app->user->id;
        $productStock->status = ProductStock::STATUS_ACTIVE;
        $productStock->created_at = DateTimeUtility::getDate($productStock->created_at, 'Y-m-d H:i:s');
        $productStock->status = ProductStock::STATUS_REJECT;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($productStock->save()) {
                $params = Json::decode($productStock->params);
                $ref = $params['ref'];

                $productStockOutlet = ProductStockOutlet::findOne($ref);
                $productStockOutlet->status = ProductStockOutlet::STATUS_REJECTED;
                if ($productStockOutlet->save()) {

                    $productItems = ProductStatementOutlet::find()->where([
                        'type' => ProductStatementOutlet::TYPE_TRANSFER,
                        'reference_id' => $ref
                    ])->all();

                    $rows = [];

                    foreach ($productItems as $item) {

                        $rows[] = [
                            $item->outlet_id,
                            $item->item_id,
                            $item->brand_id,
                            $item->size_id,
                            abs($item->quantity),
                            'Reject',
                            'Outlet to Stock Transfer Rejected',
                            $productStock->product_stock_id,
                            Yii::$app->user->id,
                            DateTimeUtility::getDate(null, 'Y-m-d H:i:s'),
                            DateTimeUtility::getDate(null, 'Y-m-d H:i:s')
                        ];
                    }

                    $totalRecord = Yii::$app->db->createCommand()->batchInsert(ProductStatementOutlet::tableName(), [
                        'outlet_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks',
                        'reference_id', 'user_id', 'created_at', 'updated_at'],
                        $rows
                    )->execute();

                    if ($totalRecord > 0) {
                        $message = "Stock transfer has been rejected";
                        FlashMessage::setMessage($message, "Stock Reject", "info");
                        $transaction->commit();
                    } else {
                        $message = "Stock received has not been reject bcoz of internal errors.";
                        FlashMessage::setMessage($message, "Stock Reject", "info");
                        $transaction->rollBack();

                    }

                }

            }
        } catch (\Exception $e) {
            $message = "Stock received Exception";
            FlashMessage::setMessage($message, "Stock Received", "info");
            $transaction->rollBack();
        }

        return $this->redirect(['index']);

    }

    public function actionReceivedApproved($id)
    {
        $id = Utility::decrypt($id);
        $productStock = ProductStock::findOne($id);
        $productStock->user_id = Yii::$app->user->id;
        $productStock->status = ProductStock::STATUS_ACTIVE;
        $productStock->created_at = DateTimeUtility::getDate($productStock->created_at, 'Y-m-d H:i:s');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($productStock->save()) {
                $params = Json::decode($productStock->params);
                $ref = $params['ref'];

                $productStockOutlet = ProductStockOutlet::findOne($ref);
                $productStockOutlet->status = ProductStockOutlet::STATUS_ACTIVE;
                if ($productStockOutlet->save()) {
                    $productItems = ProductStatementOutlet::find()->where([
                        'type' => ProductStatementOutlet::TYPE_TRANSFER,
                        'reference_id' => $ref
                    ])->all();

                    $rows = [];

                    foreach ($productItems as $item) {
                        $rows[] = [
                            $item->item_id,
                            $item->brand_id,
                            $item->size_id,
                            abs($item->quantity),
                            'Stock-Received',
                            $item->remarks ? $item->remarks : 'Received',
                            $productStock->product_stock_id,
                            Yii::$app->user->id,
                            DateTimeUtility::getDate(null, 'Y-m-d H:i:s'),
                            DateTimeUtility::getDate(null, 'Y-m-d H:i:s')
                        ];
                    }

                    $totalRecord = Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), [
                        'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks',
                        'reference_id', 'user_id', 'created_at', 'updated_at'],
                        $rows
                    )->execute();

                    if ($totalRecord > 0) {
                        $message = "Stock received has been approved successfully";
                        FlashMessage::setMessage($message, "Stock Received", "info");
                        $transaction->commit();
                    } else {
                        $message = "Stock received has not been approved successfully";
                        FlashMessage::setMessage($message, "Stock Received", "info");
                        $transaction->rollBack();

                    }

                }

            }
        } catch (\Exception $e) {
            $message = "Stock received Exception";
            FlashMessage::setMessage($message, "Stock Received", "info");
            $transaction->rollBack();
        }

        return $this->redirect(['index']);

    }

    public function actionReceivedView($id)
    {
        $searchModel = new ProductStockItemsSearch();
        $searchModel->product_stock_id = Utility::decrypt($id);
        return $this->renderAjax('product-stock-received-items', [
            'id' => $id,
            'dataProvider' => $searchModel->view(),
        ]);
    }


    public function actionDiscard($type, $source)
    {
        ProductStockItemsDraft::deleteAll(['user_id' => Yii::$app->user->getId(), 'type' => $type, 'source' => $source]);
        return $this->redirect(['index']);
    }

    /**
     * @param $StockId
     * @throws \yii\db\Exception
     */
    private function itemMoveToDraftTable($StockId, $source = 'Stock')
    {

        $userId = Yii::$app->user->getId();
        $draft = ProductStockItemsDraft::find()->where(['user_id' => $userId])->one();

        if (!$draft) {
            $data = [];
            $items = ProductStockItems::find()->where(['product_stock_id' => $StockId])->all();
            foreach ($items as $item) {
                $data[] = [
                    $item->product_stock_items_id,
                    $item->product_stock_id,
                    $userId,
                    $item->item_id,
                    $item->brand_id,
                    $item->size_id,
                    $item->cost_price,
                    $item->wholesale_price,
                    $item->retail_price,
                    $item->new_quantity,
                    0,
                    'update',
                    $source
                ];
            }

            Yii::$app->db->createCommand()->batchInsert('product_stock_items_draft',
                ['product_stock_items_id', 'product_stock_id', 'user_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price', 'new_quantity', 'alert_quantity', 'type', 'source'],
                $data
            )->execute();
        }
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
