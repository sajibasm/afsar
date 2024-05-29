<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;



use app\models\CashBook;
use app\models\CustomerAccount;
use app\models\DepositBook;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\Serialize;
use Yii;

class TransactionStore
{

    public static function receivedPayment()
    {

    }
    
    public static function sales($salesId)
    {

        $model = Sales::findOne($salesId);

        Serialize::add($model->sales_id , Sales::tableName(), $model, Serialize::STATUS_PENDING);

        $salesDetails = SalesDetails::find()->where(['sales_id'=>$model->sales_id])->all();
        foreach ($salesDetails as $salesDetail){
            Serialize::add($model->sales_id , SalesDetails::tableName(), $salesDetail, Serialize::STATUS_PENDING);
        }

        $customerAccounts = CustomerAccount::find()->where(['sales_id'=>$model->sales_id])->all();
        foreach ($customerAccounts as $customerAccount){
            Serialize::add($model->sales_id , CustomerAccount::tableName(), $customerAccount, Serialize::STATUS_PENDING);
        }

        $cashBook = CashBook::find()->where(['reference_id'=>$model->sales_id, 'source'=>CashBook::SOURCE_SALES])->one();
        if($cashBook){
            Serialize::add($model->sales_id , CashBook::tableName(), $cashBook, Serialize::STATUS_PENDING);
        }else{
            $depositBook = DepositBook::find()->where(['reference_id'=>$model->sales_id, 'source'=>DepositBook::SOURCE_SALES])->one();
            if($depositBook){
                Serialize::add($model->sales_id , DepositBook::tableName(), $depositBook, Serialize::STATUS_PENDING);
            }
        }
    }

}