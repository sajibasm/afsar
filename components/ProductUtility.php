<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\Brand;
use app\models\BrandNew;
use app\models\ClientSalesPayment;
use app\models\Item;
use app\models\ProductItemsPrice;
use app\models\ProductStatement;
use app\models\ProductStock;
use app\models\ProductStockItems;
use app\models\ProductUnit;
use app\models\Sales;
use app\models\SalesDraft;
use app\models\Size;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ProductUtility {

    public static function getAvailableProduct()
    {

    }

    public static function getBrandListMap( $status = 'active', $orderByColumn = 'name', $asArray = false)
    {
        $records =  BrandNew::find()->where(['status'=>$status])->orderBy($orderByColumn)->all();
        if($asArray){
            return ArrayHelper::map($records, 'id', 'name');
        }
        return $records;
    }


    public static function getItemListByBrand($brandId, $status = 'active', $orderByColumn = 'item_name', $asArray = false)
    {
        $cacheKey = "itemListByBrand:{$brandId}:{$status}:{$orderByColumn}:{$asArray}";

        return Yii::$app->cache->getOrSet($cacheKey, function () use ($brandId, $status, $orderByColumn, $asArray) {
            $itemIds = Brand::find()
                ->select('item_id')
                ->where(['brand_id' => $brandId])
                ->column();

            $query = Item::find()
                ->where(['product_status' => $status])
                ->andWhere(['item_id' => $itemIds])
                ->orderBy($orderByColumn);

            $records = $query->all();

            return $asArray ? ArrayHelper::map($records, 'item_id', 'item_name') : $records;
        }, 3600, new TagDependency(['tags' => "itemListByBrand:{$brandId}"]));
    }


    public static function getItemList( $status = 'active', $orderByColumn = 'item_name', $asArray = false)
    {
        $records =  Item::find()->where(['product_status'=>$status])->orderBy($orderByColumn)->all();
        if($asArray){
            return ArrayHelper::map($records, 'item_id', 'item_name');
        }
        return $records;
    }

    public static function getBrandList( $status = 'active', $orderByColumn = 'brand_name', $asArray = false)
    {
        $records =  Brand::find()->where(['brand_status'=>$status])->orderBy($orderByColumn)->all();
        if($asArray){
            return ArrayHelper::map($records, 'brand_id', 'brand_name');
        }
        return $records;
    }

    public static function getSizeList( $status = 'active', $orderByColumn = 'size_name', $asArray = false)
    {

        $records =  Size::find()->where(['size_status'=>$status])->orderBy($orderByColumn)->all();
        if($asArray){
            return ArrayHelper::map($records, 'size_id', 'size_name');
        }
        return $records;
    }

    public static function getBrandListByItem($itemId, $status = 'active', $orderByColumn = 'brand_name', $asArray = false)
    {
        $cacheKey = "brandListByItem:{$itemId}:{$status}:{$orderByColumn}:{$asArray}";
        return Yii::$app->cache->getOrSet($cacheKey, function () use ($itemId, $status, $orderByColumn, $asArray) {
            $records = Brand::find()
                ->where(['item_id' => $itemId, 'brand_status' => $status])
                ->orderBy($orderByColumn)
                ->all();
            return $asArray ? ArrayHelper::map($records, 'brand_id', 'brand_name') : $records;
        }, 3600, new TagDependency(['tags' => "brandListByItem:{$itemId}"]));
    }

    public static function getSizeListByBrand($itemId, $brandId, $status = 'active', $orderByColumn = 'size_name', $asArray = false)
    {
        $cacheKey = "sizeList:{$itemId}:{$brandId}:{$status}:{$orderByColumn}:{$asArray}";

        return Yii::$app->cache->getOrSet($cacheKey, function () use ($itemId, $brandId, $status, $orderByColumn, $asArray) {
            $records = Size::find()
                ->where([
                    'item_id' => $itemId,
                    'brand_id' => $brandId,
                    'size_status' => $status,
                ])
                ->orderBy($orderByColumn)
                ->all();

            return $asArray ? ArrayHelper::map($records, 'size_id', 'size_name') : $records;
        }, 3600, new TagDependency(['tags' => "sizeList:{$itemId}:{$brandId}"]));
    }

    public static function getTotalQuantity($sizeId)
    {
        $cacheKey = "totalQuantity:size:{$sizeId}";

        return Yii::$app->cache->getOrSet($cacheKey, function () use ($sizeId) {
            return ProductStatement::find()
                ->where(['size_id' => $sizeId])
                ->sum('quantity') ?: 0;
        }, 3600, new TagDependency(['tags' => "totalQuantity:size:{$sizeId}"]));
    }

    public static function getDraftProductQuantity($sizeId)
    {
        if (empty($sizeId)) {
            return 0;
        }

        $cacheKey = "draftQuantity:size:{$sizeId}";

        return Yii::$app->cache->getOrSet($cacheKey, function () use ($sizeId) {
            return SalesDraft::find()
                ->where([
                    'and',
                    ['size_id' => $sizeId],
                    ['or',
                        ['type' => SalesDraft::TYPE_INSERT],
                        ['type' => SalesDraft::TYPE_UPDATE_ADDED]
                    ]
                ])
                ->sum('quantity') ?: 0;
        }, 3600, new TagDependency(['tags' => "draftQuantity:size:{$sizeId}"]));
    }

    public static function getProductStockPrice($sizeId)
    {
        if (empty($sizeId)) {
            return false;
        }

        $cacheKey = "productStockPrice:size:{$sizeId}";

        return Yii::$app->cache->getOrSet($cacheKey, function () use ($sizeId) {
            $productItemPrice = ProductItemsPrice::find()
                ->where(['size_id' => $sizeId])
                ->one();

            return $productItemPrice ?: false;
        }, 3600, new TagDependency(['tags' => "productStockPrice:size:{$sizeId}"]));
    }

    public static function getProductUnit($asArray = false)
    {
        $record = ProductUnit::find()->where(['status'=>ProductUnit::STATUS_ACTIVE])->orderBy('name')->all();

        if($asArray){
            return ArrayHelper::map($record, 'id', 'name');
        }
        return $record;
    }

    public static function getInvoiceHasDue($customerId)
    {
        return Sales::findAll(CustomerUtility::getInvoiceListByCustomer($customerId));
    }

    public static function getProductStatementType()
    {
        return ProductStatement::find()->select('product_statement_id, type')->distinct('type')->orderBy('type')->all();
    }

    public static function getPriceWthQuantityBySize($sizeId){
        $out = ['success'=>false, 'cost' => 0, 'wholesale' => 0, 'retail' => 0, 'quantity'=>0, 'message'=>'Unable to getting product in inventory'];
        $stockPrice = ProductUtility::getProductStockPrice($sizeId);
        $qty = ProductUtility::getTotalQuantity($sizeId) - ProductUtility::getDraftProductQuantity($sizeId);
        if ($stockPrice) {
            $quantity = doubleval($qty);
            return [
                'success'   => $quantity > 0,
                'cost'      => $stockPrice->wholesale_price,
                'wholesale' => $stockPrice->wholesale_price,
                'retail'    => $stockPrice->retail_price,
                'quantity'  => $quantity,
                'message'   => $quantity > 0
                    ? 'In stock: ' . $quantity . ' units available.'
                    : 'Out of stock. Please restock this item.'
            ];
        }

        return [
            'success'   => false,
            'cost'      => 0,
            'wholesale' => 0,
            'retail'    => 0,
            'quantity'  => 0,
            'message'   => 'Stock data not found or unavailable at this time.'
        ];
    }

    public static function stockMovementApproved(ProductStock $model )
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->created_at = DateTimeUtility::getDate($model->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
            $model->status = ProductStock::STATUS_ACTIVE;
            if($model->save()){
                $productItemsStatement = [];
                $productItemsColumn = ['product_statement_id','item_id','brand_id','size_id','quantity','type','remarks','reference_id','user_id','created_at','updated_at'];
                $productStockItems = ProductStockItems::find()->where(['product_stock_id'=>$model->product_stock_id])->all();
                $count = count($productStockItems);

                foreach ($productStockItems as $item){
                    $item->previous_quantity = ProductUtility::getTotalQuantity($item->size_id);
                    $item->total_quantity = ($item->previous_quantity + $item->new_quantity);
                    $item->status = ProductStockItems::STATUS_DONE;
                    if($item->save()){
                        $productItemPrice = ProductItemsPrice::findOne(['size_id'=>$item->size_id]);
                        if(!$productItemPrice){
                            $productItemPrice = new ProductItemsPrice();
                            $productItemPrice->quantity  = $item->new_quantity;
                        }else{
                            $productItemPrice->quantity += $item->new_quantity;
                        }

                        $productItemPrice->item_id = $item->item_id;
                        $productItemPrice->brand_id = $item->brand_id;
                        $productItemPrice->size_id = $item->size_id;
                        $productItemPrice->cost_price  = $item->cost_price ;
                        $productItemPrice->wholesale_price  = $item->wholesale_price;
                        $productItemPrice->retail_price    = $item->retail_price;
                        $productItemPrice->alert_quantity  = $item->new_quantity;

                        if($productItemPrice->save()){
                            $productItemsStatement[] = [
                                null,
                                $item->item_id,
                                $item->brand_id,
                                $item->size_id,
                                $item->new_quantity,
                                ProductStatement::TYPE_STOCK_TRANSFER,
                                $model->remarks,
                                $model->product_stock_id,
                                1,
                                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'),
                                DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka')
                            ];
                        }
                    }
                }
                $insert  = \Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), $productItemsColumn, $productItemsStatement)->execute();
                if($count==$insert){
                    $transaction->commit();
                    return ['success'=>true, 'data'=>['refId'=>$model->product_stock_id]];
                }else{
                    return ['success'=>false, 'data'=>"Insert Problem Bulk {$insert}"];
                }
            }else{
                $transaction->rollBack();
                return ['success'=>false, 'data'=>"Stock Save {$model->getErrors()}"];
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Exception {$e}"];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Thr Exception {$e}"];
        }
    }

    public static function stockMovementPrevious($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $count = 0;
            $stock = new ProductStock();
            $params = [
                'refOutlet'=>[
                    "id"=>1,
                    "name"=>"Head Office",
                    "api"=>"http://localhost/online/web/api",
                    "accessToken"=>"",
                    "self"=>true
                ],
                "outlet"=>"2",
                "refId"=>5,
                "send"=>true,
                "user"=> [
                    "id"=>1,
                    "name"=>"superadmin"
                ]
            ];
            $stock->remarks = 'ref#2 user: superadmin';
            $stock->user_id = 1;
            $stock->params = Json::encode($params);
            $stock->type = ProductStock::TYPE_TRANSFER;
            $stock->created_at =  DateTimeUtility::getDate($stock->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
            $stock->updated_at =  DateTimeUtility::getDate($stock->updated_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
            $stock->status = ProductStock::STATUS_ACTIVE;
            if($stock->save()){
                $productItemsStatement = [];
                $productStockItemsData = [];

               $productStatementModel = new ProductStatement();
               $productStockItemsModel = new ProductStockItems();

                foreach ($data->product as $item){
                    $productItemsStatement[] = [
                        null,
                        $item->item_id,
                        $item->brand_id,
                        $item->size_id,
                        -$item->quantity,
                        ProductStatement::TYPE_STOCK_TRANSFER,
                        $stock->remarks,
                        $stock->product_stock_id,
                        1,
                        DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'),
                        DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka')
                    ];
                    $currentQuantity = (int) ProductUtility::getTotalQuantity($item->size_id);
                    $productStockItemsData[] = [
                        null,
                        $stock->product_stock_id,
                        $item->item_id,
                        $item->brand_id,
                        $item->size_id,
                        $item->cost_price,
                        $item->wholesale_price,
                        $item->retail_price,
                        $currentQuantity,
                        $item->quantity,
                        $currentQuantity - $item->quantity,
                        ProductStockItems::STATUS_DONE
                    ];
                }

                $insert  = Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), array_keys($productStatementModel->getAttributes()), $productItemsStatement)->execute();
                $insert2 = Yii::$app->db->createCommand()->batchInsert(ProductStockItems::tableName(), array_keys($productStockItemsModel->getAttributes()), $productStockItemsData)->execute();

                if($insert2==$insert){
                    $transaction->commit();
                    return ['success'=>true, 'data'=>['refId'=>$stock->product_stock_id]];
                }else{
                    $transaction->rollBack();
                    return ['success'=>false, 'data'=>"Insert Problem {$insert}"];
                }
            }else{
                $transaction->rollBack();
                return ['success'=>false, 'data'=>$stock->getErrors()];
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Exception {$e}"];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Thr Exception {$e}"];
        }
    }

    public static function stockTransferReject($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $count = 0;
            $stock = ProductStock::findOne($data->stockId);
            $params = Json::decode($stock->params);
            $params['user'] = $data->user;
            $stock->remarks = 'ref#'.$data->refId.' user:'.$data->user->name;
            $stock->params = Json::encode($params);
            $stock->type = ProductStock::TYPE_TRANSFER;
            $stock->created_at =  DateTimeUtility::getDate($stock->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
            $stock->updated_at =  DateTimeUtility::getDate($stock->updated_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
            $stock->status = ProductStock::STATUS_REJECT;
            if($stock->save()){
                $productItemsStatement = [];
                $productItemsColumn = ['product_statement_id','item_id','brand_id','size_id','quantity','type','remarks','reference_id','user_id','created_at','updated_at'];
                $productStockItems = ProductStockItems::find()->where(['product_stock_id'=>$stock->product_stock_id])->all();
                $count = count($productStockItems);
                foreach ($productStockItems as $item){
                    $item->status = ProductStockItems::STATUS_DONE;
                    if($item->save()){
                        $productItemsStatement[] = [
                            null,
                            $item->item_id,
                            $item->brand_id,
                            $item->size_id,
                            $item->new_quantity,
                            ProductStatement::TYPE_STOCK_TRANSFER_REJECT,
                            $stock->remarks,
                            $stock->product_stock_id,
                            1,
                            DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'),
                            DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka')
                        ];
                    }
                }

                $insert  = Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), $productItemsColumn, $productItemsStatement)->execute();
                if($count==$insert){
                    $transaction->commit();
                    return ['success'=>true, 'data'=>['refId'=>$stock->product_stock_id]];
                }else{
                    $transaction->rollBack();
                    return ['success'=>false, 'data'=>"Insert Problem {$insert}"];
                }
            }else{
                $transaction->rollBack();
                return ['success'=>false, 'data'=>"Stock Save {$stock->getErrors()}"];
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Exception {$e}"];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Thr Exception {$e}"];
        }
    }

    public static function stockTransferApprove($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $stock = ProductStock::findOne($data->stockId);
            if($stock){
                $params = Json::decode($stock->params);
                $params['user'] = $data->user;
                $stock->remarks = 'ref#'.$data->refId.' Approved:'.$data->user->name;
                $stock->params = Json::encode($params);
                $stock->type = ProductStock::TYPE_TRANSFER;
                $stock->created_at =  DateTimeUtility::getDate($stock->created_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
                $stock->updated_at =  DateTimeUtility::getDate($stock->updated_at, 'Y-m-d H:i:s', 'Asia/Dhaka');
                $stock->status = ProductStock::STATUS_ACTIVE;
                if($stock->save()){
                    $items = ProductStockItems::updateAll(['status'=>ProductStockItems::STATUS_DONE], ['product_stock_id'=>$stock->product_stock_id]);
                    if($items>0){
                        $transaction->commit();
                        return  ['success'=>true, 'data'=>"success"];
                    }
                }else{
                    return ['success'=>false, 'data'=>"Unable to update product stock.".$stock->getErrors()];
                }
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Exception {$e}"];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Thr Exception {$e}"];
        }
    }


    public static function stockAccept($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $stock = new ProductStock();
            $stock->user_id = 1;
            $stock->invoice_no = $data->invoice_no;
            $stock->remarks = 'ref#'.$data->stockId.' user:'.$data->params->user->name. ' Msg: '.$data->remarks;
            $stock->params = Json::encode( ['ref'=>$data->stockId, 'user'=>$data->params->user, 'refOutlet'=>$data->params->refOutlet]);
            $stock->type = ProductStock::TYPE_MOVEMENT;
            $stock->status = ProductStock::STATUS_PENDING;
            if($stock->save()){
                $productItemsColumn = ['product_stock_items_id', 'product_stock_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity', 'status'];
                $productItems = [];
                foreach ($data->product as $product){
                    $productUtility = ProductUtility::getPriceWthQuantityBySize($product->size_id);
                    $productItems[] = [
                        null,
                        $stock->product_stock_id,
                        $product->item_id,
                        $product->brand_id,
                        $product->size_id,
                        $product->cost_price,
                        $product->wholesale_price,
                        $product->retail_price,
                        $productUtility['quantity'],
                        $product->quantity,
                        ($productUtility['quantity']+$product->quantity),
                        ProductStockItems::STATUS_PENDING
                    ];
                }
                $insert  = Yii::$app->db->createCommand()->batchInsert(ProductStockItems::tableName(), $productItemsColumn, $productItems)->execute();
                if(count($data->product)==$insert){
                    $transaction->commit();
                    return ['success'=>true, 'data'=>['refId'=>$stock->product_stock_id]];
                }else{
                    $transaction->rollBack();
                    return ['success'=>false, 'data'=>"Insert Problem {$insert}"];
                }
            }else{
                $transaction->rollBack();
                return ['success'=>false, 'data'=>"Stock Save {$stock->getErrors()}"];
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Exception {$e}"];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Thr Exception {$e}"];
        }
    }

    public static function stockMovement($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $stock = new ProductStock();
            $stock->user_id = 1;
            $stock->invoice_no = $data->invoice_no;
            $stock->remarks = 'ref#'.$data->stockId.' user:'.$data->params->user->name. ' Msg: '.$data->remarks;
            $stock->params = Json::encode( ['ref'=>$data->stockId, 'user'=>$data->params->user, 'refOutlet'=>$data->params->refOutlet]);
            $stock->type = ProductStock::TYPE_MOVEMENT;
            $stock->status = ProductStock::STATUS_PENDING;
            if($stock->save()){
                $productItemsColumn = ['product_stock_items_id', 'product_stock_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity', 'status'];
                $productItems = [];
                foreach ($data->product as $product){
                    $productUtility = ProductUtility::getPriceWthQuantityBySize($product->size_id);
                    $productItems[] = [
                        null,
                        $stock->product_stock_id,
                        $product->item_id,
                        $product->brand_id,
                        $product->size_id,
                        $product->cost_price,
                        $product->wholesale_price,
                        $product->retail_price,
                        $productUtility['quantity'],
                        $product->quantity,
                        ($productUtility['quantity']+$product->quantity),
                        ProductStockItems::STATUS_PENDING
                    ];
                }
                $insert  = Yii::$app->db->createCommand()->batchInsert(ProductStockItems::tableName(), $productItemsColumn, $productItems)->execute();
                if(count($data->product)==$insert){
                    $transaction->commit();
                    return ['success'=>true, 'data'=>['refId'=>$stock->product_stock_id]];
                }else{
                    $transaction->rollBack();
                    return ['success'=>false, 'data'=>"Insert Problem {$insert}"];
                }
            }else{
                $transaction->rollBack();
                return ['success'=>false, 'data'=>"Stock Save {$stock->getErrors()}"];
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Exception {$e}"];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success'=>false, 'data'=>"Thr Exception {$e}"];
        }
    }

    public static function generateStockMovementData($stock){
        $data = [];
        $products = ProductStockItems::find()->where(['product_stock_id'=>$stock->product_stock_id, 'status'=>ProductStockItems::STATUS_PENDING])->all();
        $data = [
            'stockId'=>$stock->product_stock_id,
            'invoice_no'=>$stock->invoice_no,
            'type'=>$stock->type,
            'params'=>Json::decode($stock->params),
            'remarks'=>$stock->remarks
        ];
        $data['params']['user'] = [
            'id'=>Yii::$app->user->getId(),
            'name'=>$data['params']['user']['name']= Yii::$app->user->identity->username
        ];
        foreach ($products as $product){
            $data['product'][] = [
                'item_id'=>$product->item_id,
                'brand_id'=>$product->brand_id,
                'size_id'=>$product->size_id,
                'cost_price'=>$product->cost_price,
                'wholesale_price'=>$product->wholesale_price,
                'retail_price'=>$product->retail_price,
                'quantity'=>$product->new_quantity,
            ];
        }
        return $data;
    }

}
