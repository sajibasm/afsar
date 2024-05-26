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

        if (Yii::$app->request->isGet) {
            $model = ProductItemsPrice::find()->where(['size_id' => $sizeId])->one();
            if ($model) {
                return [
                    'success' => false,
                    'cost' => number_format($model->cost_price),
                    'wholesale' => number_format($model->wholesale_price),
                    'retail' => number_format($model->retail_price),
                    'alert' => number_format($model->alert_quantity),
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
    private function addItemDraft(ProductStockItemsDraft $model, $data = [], $souce = ProductStockItemsDraft::SOURCE_MOVEMENT)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model->load($data);
        $model->source = $souce;
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
                        if (ProductStock::stockSave($productStock)) {
                            $transaction->commit();
                            FlashMessage::setMessage("New Stock #" . $productStock->invoice_no . " has been added.", "New Stock", "success");
                            return $this->redirect(['index']);
                        }

                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('register/create', [
                'model' => $model,
                'productStock' => $productStock,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }


        return $this->render('register/create', [
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
        $this->deleteDraft(ProductStockItemsDraft::TYPE_UPDATE, ProductStockItemsDraft::SOURCE_STOCK);
        $searchModel = new ProductStockItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);

        return $this->render('items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return array|string
     */
    public function actionItemUpdate($id)
    {

        $model = ProductStockItemsDraft::findOne($id);
        $model->itemName = $model->item->item_name;
        $model->brandName = $model->brand->brand_name;
        $model->sizeName = $model->size->size_name;

        if ($model->load(Yii::$app->request->post())) {
            $data = ['error' => false, 'message' => 'success'];
            if ($model->save()) {
                $data = ['error' => false, 'message' => 'success'];
                Yii::$app->session->setFlash('success', 'Size: <strong>' . $model->item->item_name . ' ' . $model->brand->brand_name . ' ' . $model->size->size_name . '</strong> has been updated.');
            } else {
                $data = ['error' => true, 'message' => ActiveForm::validate($model)];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        }

        return $this->renderAjax('register/_items', ['model' => $model,]);
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
        $model->type = 'update';
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
                $productStock = $this->stockUpdate($productStock);
                //Yii::$app->session->setFlash('success', 'New Stock# <strong>'.$productStock->product_stock_id.'</strong> updated.');
                return $this->redirect(['index']);
            }
        }


        return $this->render('register/create', [
            'model' => $model,
            'productStock' => $productStock,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionTransferToOutlet($id)
    {
        $id = Utility::decrypt($id);
        $this->itemMoveToDraftTable($id, ProductStockItemsDraft::SOURCE_MOVEMENT);

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
                $productStock->params = Json::encode(['receivedOutlet' => $outlet->name, 'coreStock' => $id]);

                try {
                    if ($productStock->save()) {
                        $isSaveStockItems = ProductStockItemsOutlet::draftToStockItems($productStock->product_stock_id, $items);
                        $isSaveStockOutlet = ProductStockOutlet::saveOutletStock($id, $productStock, $data, $items);
                        $updateStock = ProductStock::updateAll(['params' => ProductStock::TYPE_TRANSFER], ['product_stock_id' => $id]);
                        if ($isSaveStockItems && $isSaveStockOutlet && $updateStock) {
                            $transaction->commit();
                            $message = "Stock Transfer# " . $productStock->invoice_no . "has been created.";
                            FlashMessage::setMessage($message, "Stock Transfer To Outlet", "info");
                            return $this->redirect(['index']);
                        }
                    }
                } catch (\Exception $exception) {
                    $transaction->rollBack();
                }
            }
        }


        return $this->render('transfer-to-outlet/create', [
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
     * Updates an existing ProductStockItemsDraft model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionStockDelete()
    {
        $data = Yii::$app->request->get();

        if (isset($data['id']) && !empty($data['id']) && isset($data['action']) && !empty($data['action'])) {

            if ($data['action'] == 'create') {
                ProductStockItemsDraft::deleteAll(['product_stock_items_draft_id' => $data['id']]);
            } else {
                $draftItems = ProductStockItemsDraft::find()->where(['product_stock_items_draft_id' => $data['id']])->one();
                $productItemPrice = ProductItemsPrice::find()->where(['size_id' => $draftItems->size_id])->one();
                $productItemPrice->quantity = $productItemPrice->quantity - $draftItems->new_quantity;
                $productItemPrice->save();
                ProductStockItemsDraft::deleteAll(['product_stock_items_draft_id' => $data['id']]);
                ProductStatement::deleteAll(['reference_id' => $draftItems->product_stock_id, 'size_id' => $draftItems->size_id, 'type' => ProductStatement::TYPE_STOCK]);
                ProductStockItems::deleteAll(['product_stock_items_id' => $draftItems->product_stock_items_id]);
            }
        }

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
                $data = Yii::$app->request->post();
                $sizeId = $data['ProductStockItemsDraft']['size_id'];
                $qty = ProductUtility::getTotalQuantity($sizeId) - ProductUtility::getDraftProductQuantity($sizeId);
                if ($qty > 0) {
                    $data['ProductStockItemsDraft']['type'] = ProductStockItemsDraft::TYPE_INSERT;
                    return $this->addItemDraft($model, $data, ProductStockItemsDraft::SOURCE_TRANSFER);
                }
            } else {
                $data = Yii::$app->request->post();
                $transaction = Yii::$app->db->beginTransaction();
                $items = ProductStockItemsDraft::findAll(['source' => ProductStockItemsDraft::SOURCE_TRANSFER, 'user_id' => Yii::$app->user->id]);
                $productStock->load($data);
                $outlet = Outlet::findOne($productStock->outlet);
                $productStock->params = Json::encode([]);

                try {
                    if ($productStock->save()) {
                        $isSaveStockItems = ProductStock::draftToStockItems($productStock->product_stock_id, $items);
                        $isSaveStockOutlet = ProductStock::saveOutletStock($productStock, $data, $items, $outlet);
                        if ($isSaveStockItems && $isSaveStockOutlet) {
                            $transaction->commit();
                            $message = "Stock Transfer# " . $productStock->invoice_no . "has been created.";
                            FlashMessage::setMessage($message, "Stock Transfer To Outlet", "info");
                            return $this->redirect(['index']);
                        }
                    }
                } catch (\Exception $exception) {
                    $transaction->rollBack();
                }
            }
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

    public function actionStockDeleteAll($type = ProductStockItemsDraft::TYPE_INSERT, $source = ProductStockItemsDraft::SOURCE_STOCK)
    {
        $this->deleteDraft($type, $source);
        return $this->redirect(['index']);
    }

    public function actionDiscard($type = ProductStockItemsDraft::TYPE_INSERT, $source = ProductStockItemsDraft::SOURCE_STOCK)
    {
        $this->deleteDraft($type, $source);
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
     * Creates a new ProductStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param ProductStock $model
     * @return mixed
     */
    private function stockUpdate(ProductStock $model)
    {
        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {

                $productStockItemsDraft = ProductStockItemsDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'type' => 'update'])->all();

                foreach ($productStockItemsDraft as $item) {

                    $productStockItemsModel = new ProductStockItems();
                    $isNewProduct = false;
                    $quantity = 0;

                    //if new product added in existing invoice.
                    if ((int)$item->product_stock_items_id == 0) {
                        $isNewProduct = true;
                    } else {
                        $productStockItemsModel = ProductStockItems::find()->where(['product_stock_id' => $item->product_stock_id, 'product_stock_items_id' => $item->product_stock_items_id])->one();
                        $quantity = $item->new_quantity - $productStockItemsModel->new_quantity;
                    }

                    $productStockItemsModel->product_stock_id = $model->product_stock_id;
                    $productStockItemsModel->item_id = $item->item_id;
                    $productStockItemsModel->brand_id = $item->brand_id;
                    $productStockItemsModel->size_id = $item->size_id;
                    $productStockItemsModel->cost_price = $item->cost_price;
                    $productStockItemsModel->wholesale_price = $item->wholesale_price;
                    $productStockItemsModel->retail_price = $item->retail_price;

                    if ($isNewProduct) {
                        $productStockItemsModel->new_quantity = $item->new_quantity;
                        $productStockItemsModel->total_quantity = $productStockItemsModel->new_quantity + $productStockItemsModel->previous_quantity;
                        $productStockItemsModel->previous_quantity = ProductUtility::getTotalQuantity($item->size_id);
                    } else {
                        $productStockItemsModel->new_quantity += $quantity;
                        $productStockItemsModel->total_quantity += $quantity;
                    }


                    if ($productStockItemsModel->save()) {

                        $productStatement = new ProductStatement();
                        if (!$isNewProduct) {
                            $productStatement = ProductStatement::find()->where(['reference_id' => $item->product_stock_id, 'size_id' => $item->size_id, 'type' => ProductStatement::TYPE_STOCK])->one();
                        }

                        $productStatement->item_id = $item->item_id;
                        $productStatement->brand_id = $item->brand_id;
                        $productStatement->size_id = $item->size_id;
                        $productStatement->quantity = $item->new_quantity;
                        $productStatement->type = ProductStatement::TYPE_STOCK;
                        $productStatement->remarks = 'success';
                        $productStatement->reference_id = $model->product_stock_id;
                        $productStatement->user_id = 1;
                        if ($productStatement->save()) {

                            $productItemsPrice = ProductItemsPrice::find()->where(['size_id' => $item->size_id])->one();

                            if (isset($productItemsPrice->product_stock_items_id)) {
                                $productItemsPrice->cost_price = $item->cost_price;
                                $productItemsPrice->wholesale_price = $item->wholesale_price;
                                $productItemsPrice->retail_price = $item->retail_price;
                                $productItemsPrice->alert_quantity = $item->alert_quantity;
                                if ($isNewProduct) {
                                    $productItemsPrice->quantity = $productItemsPrice->quantity + $item->new_quantity;
                                } else {
                                    $productItemsPrice->quantity = $productItemsPrice->quantity + $quantity;
                                }
                            } else {
                                $productItemsPrice = new ProductItemsPrice();
                                $productItemsPrice->item_id = $item->item_id;
                                $productItemsPrice->brand_id = $item->brand_id;
                                $productItemsPrice->size_id = $item->size_id;
                                $productItemsPrice->cost_price = $item->cost_price;
                                $productItemsPrice->wholesale_price = $item->wholesale_price;
                                $productItemsPrice->retail_price = $item->retail_price;
                                $productItemsPrice->quantity = $item->new_quantity;
                                $productItemsPrice->alert_quantity = $item->alert_quantity;
                            }

                            if ($productItemsPrice->save()) {
                                $this->actionStockDeleteAll('update');
                            }
                        }
                    }
                }
                Yii::$app->session->setFlash('success', 'Product Stock: <strong>' . $model->product_stock_id . '</strong> has been updated.');
            }
        }
        return $model;
    }

    private function deleteDraft($type, $source)
    {
        return ProductStockItemsDraft::deleteAll(['user_id' => Yii::$app->user->getId(), 'type' => $type, 'source' => $source]);
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
