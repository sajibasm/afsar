<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\Warehouse;
use yii\helpers\ArrayHelper;

class WarehouseUtility
{

    public static function getWarehouseList($order = 'warehouse_name', $asArray = false)
    {
        $records = Warehouse::find()->orderBy($order)->all();
        if($asArray) {
           return ArrayHelper::map($records, 'warehouse_id', 'warehouse_name');
        }
        return  $records;
    }

}