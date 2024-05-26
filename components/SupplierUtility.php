<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;


use app\models\Buyer;
use app\models\Client;
use yii\helpers\ArrayHelper;

class SupplierUtility
{

    public static function getSupplierList($order='name', $asArray = false)
    {
        $records =  Buyer::find()->orderBy($order)->all();
        if($asArray){
            return ArrayHelper::map($records, 'id', 'name');
        }
        return $records;
    }

}