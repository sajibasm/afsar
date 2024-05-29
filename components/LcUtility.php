<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\Lc;
use app\models\LcPaymentType;
use yii\helpers\ArrayHelper;

class LcUtility
{

    public static function getLcList($order = 'lc_name', $asArray = false)
    {
        $record = Lc::find()->orderBy($order)->all();
        if($asArray){
            return ArrayHelper::map($record, 'lc_id', 'lc_name');
        }
        return $record;
    }

    public static function getLcPaymentType($order = 'lc_payment_type_name', $status = 'active', $asArray = false)
    {
        $query = LcPaymentType::find();

        if($status=='active'){
            $query->where(['lc_payment_type_status'=>LcPaymentType::ACTIVE]);
        }elseif($status=='inactive'){
            $query->where(['lc_payment_type_status'=>LcPaymentType::INACTIVE]);
        }
        $query->orderBy($order);
        $records =  $query->all();

        if($asArray){
            return ArrayHelper::map($records, 'lc_payment_type_id', 'lc_payment_type_name');
        }
        return $records;

    }


}