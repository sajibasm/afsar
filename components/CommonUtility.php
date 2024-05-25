<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;


use app\models\Bank;
use app\models\Branch;
use app\models\Buyer;
use app\models\CashBook;
use app\models\City;
use app\models\ClientPaymentHistory;
use app\models\DepositBook;
use app\models\Lc;
use app\models\PaymentType;
use app\models\ProductStockItemsDraft;
use app\models\ReconciliationType;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;

class CommonUtility
{


    public static function debug($data, $isDataType=false ,$isExit = true)
    {

        echo "<pre>";

        if($isDataType){
            var_dump($data);
        }else{
            print_r($data);
        }

        echo "</pre>";

        if($isExit){
            die();
        }
    }

    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * @deprecated
     */
    public static function getWarehouseList()
    {
        return Warehouse::find()->orderBy('warehouse_name')->all();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * @deprecated
     */
    public static function getLcList()
    {
        return Lc::find()->orderBy('lc_name')->all();
    }

    /**
     * @return array
     */
    public static function getLcNameAndNumberArrayList()
    {
        $list = [];
        $model = Lc::find()->orderBy('lc_name')->all();
        foreach($model as $lc){
            $list[$lc->lc_id] = $lc->lc_name.' - '.$lc->lc_number;
        }

        return $list;
    }

    public static function getCityList()
    {
        return City::find()->orderBy('city_name')->all();
    }

    public static function getBuyerList()
    {
        return Buyer::find()->orderBy('name')->all();
    }

    public static function getBankPaymentType()
    {
        return PaymentType::find()->where(['type'=>PaymentType::TYPE_DEPOSIT])->orderBy('payment_type_name')->all();
    }

    public static function getPaymentTypeId($type=PaymentType::TYPE_CASH)
    {
        return PaymentType::find()->where(['type'=>PaymentType::TYPE_CASH])->one()->payment_type_id;
    }

    public static function getReconciliationType($asArray = false)
    {
        $record = ReconciliationType::find()->where(['status'=>ReconciliationType::STATUS_ACTIVE])->orderBy('name')->all();
        return $asArray?ArrayHelper::map($record, 'id', 'name'): $record;
    }

    public static function getPaymentType($asArray = false, $status='active')
    {
        if(!empty($status)){
            $record = PaymentType::find()->where(['status'=>$status])->orderBy('payment_type_name')->all();
        }else{
            $record = PaymentType::find()->orderBy('payment_type_name')->all();
        }

        if($asArray){
            return ArrayHelper::map($record, 'payment_type_id', 'payment_type_name');
        }
        return $record;
    }

    public static function getTotalStockDraftItems()
    {
        return ProductStockItemsDraft::find()->where(['user_id'=>1])->count();
    }

    public static function getBank()
    {
        return Bank::find()->orderBy('bank_name')->all();
    }

    public static function getBankById($bankId)
    {
        return Bank::findOne($bankId);
    }

    public static function getBranchById($branchId)
    {
        return Branch::findOne($branchId);
    }

    public static function getBranchByBankId($bankId)
    {
        if(!empty($bankId)){
            return Branch::find()->where(['bank_id'=>$bankId])->orderBy('branch_name')->all();
        }else{
            return [];
        }
    }

    public static function pageTotal($provider, $fieldName)
    {
        $total=0;
        foreach($provider as $item){
            $total+=$item[$fieldName];
        }
        return $total;
    }

    public static function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat);

    }

    public static function getYear()
    {
        $list = [];
        foreach(range(date('Y'),date('Y')+1) as $m){
            $list[$m] = $m;
        }
        return $list;
    }

    public static function getMonth()
    {
        $list = [];
        foreach(range(1,12) as $m){
            $list[$m] = date('F', strtotime('01-'.$m.'-'.date('Y')));
        }

        ksort($list);
        return $list;
    }

    public static function getMonthName($month)
    {
        return date('F', strtotime('01-'.$month.'-'.date('Y')));
    }

    public static function getCashBookSource()
    {
        return CashBook::find()->orderBy('source')->groupBy('source')->all();
    }

    public static function getDebitBookSource()
    {
        //Talking To Much Memory while loading
        return DepositBook::find()->orderBy('source')->groupBy('source')->all();
    }

    public static function getCustomerPaymentReceivedType()
    {
        return ClientPaymentHistory::getPaymentReceivedType();
    }




}