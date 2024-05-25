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
use app\models\Outlet;
use app\models\ProductItemsPrice;
use app\models\ProductStatement;
use app\models\ProductStatementOutlet;
use app\models\ProductStock;
use app\models\ProductStockItems;
use app\models\ProductUnit;
use app\models\Sales;
use app\models\SalesDraft;
use app\models\Size;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

class ProductOutletUtility {


    public static function getTotalQuantity($sizeId, $outletId)
    {
        return ProductStatementOutlet::find()->where(['size_id'=>$sizeId, 'outlet_id'=>$outletId])->sum('quantity')?:0;
    }

    public static function getDraftProductQuantity($sizeId, $outletId)
    {
        if(!empty($sizeId) && !empty($outletId)){
            return SalesDraft::find()->where("outletId=".$outletId." AND size_id=".$sizeId." AND type='".SalesDraft::TYPE_INSERT."'"." OR size_id=".$sizeId." AND type='".SalesDraft::TYPE_UPDATE_ADDED."'")->sum('quantity');
        }
    }


    public static function getPriceWthQuantityBySize($sizeId, $transferOutlet){

        $outlet = Outlet::findOne($transferOutlet);
        $stockPrice = ProductUtility::getProductStockPrice($sizeId);
        if($outlet->type===Outlet::TYPE_WAREHOUSE){
            $qty = ProductUtility::getTotalQuantity($sizeId);
            if ($stockPrice) {
                return [
                    'success'=>$qty>0?true:false,
                    'cost' => $stockPrice->wholesale_price,
                    'wholesale' => $stockPrice->wholesale_price,
                    'retail' => $stockPrice->retail_price,
                    'quantity' => doubleval($qty),
                    'message' => 'Quantity Available: ' . doubleval($qty) . ''
                ];
            }

        }else{
            $qty = ProductOutletUtility::getTotalQuantity($sizeId, $transferOutlet) - ProductOutletUtility::getDraftProductQuantity($sizeId, $transferOutlet);
            if ($stockPrice) {
                return [
                    'success'=>$qty>0?true:false,
                    'cost' => $stockPrice->wholesale_price,
                    'wholesale' => $stockPrice->wholesale_price,
                    'retail' => $stockPrice->retail_price,
                    'quantity' => doubleval($qty),
                    'message' => 'Quantity Available: ' . doubleval($qty) . ''
                ];
            }
        }

        return [
            'success'=>false,
            'cost' => 0,
            'wholesale' => 0,
            'retail' => 0,
            'quantity' => doubleval($qty),
            'message' => 'Quantity Available: ' . doubleval($qty) . ''
        ];

    }

}
