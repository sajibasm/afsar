<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;


use app\models\City;
use app\models\Client;
use app\models\ClientPaymentHistory;
use app\models\ClientSalesPayment;
use app\models\CustomerAccount;
use app\models\CustomerWithdraw;
use app\models\MarketBook;
use app\models\Sales;
use yii\helpers\ArrayHelper;

class CustomerUtility
{

    public static function getTotalDuesByCustomer($customerId)
    {
        $query = CustomerAccount::find();
        $query->andFilterWhere(['IN', 'client_id', $customerId]);
        $query->with('clientPaymentHistory', 'client');
        $query->select('SUM(debit) - SUM(credit) as balance, client_id');
        $query->orderBy('balance DESC');
        $balance = $query->one()['balance'];
        return $balance?$balance:0;
    }

    public static function hasWithdrawByPaymentId($paymentId)
    {
            return CustomerWithdraw::find()->where(['payment_history_id'=>$paymentId])->orderBy('id DESC')->one()->id;
    }

    public static function getInvoiceListByCustomerId($customerId, $order='client_name')
    {
        if(!empty($order)){
            return Sales::find()->where(['client_id'=>$customerId])->orderBy($order)->all();
        }

    }

    public static function getCustomerList($type=null, $order='client_name', $asArray=false)
    {
        if(!empty($type)){
            $record = Client::find()->where(['client_type'=>$type])->orderBy($order.' ASC')->all();
        }else{
            $record = Client::find()->orderBy('client_name ASC')->orderBy($order)->all();
        }

        if($asArray){
            return ArrayHelper::map($record, 'client_id', 'client_name');
        }

        return $record;
    }

    public static function getCustomerWithAddressList($type=null, $order='client_name', $asArray=false, $outlet=null)
    {

        $list = [];

        if(!empty($type)){

            if($outlet){
                $record = Client::find()->where(['client_type'=>$type, 'outletId'=>$outlet])->with('clientCity')->orderBy($order)->all();
            }else{
                $record = Client::find()->where(['client_type'=>$type])->with('clientCity')->orderBy($order)->all();

            }



        }else{
            if($outlet){
                $record = Client::find()->where(['outletId'=>$outlet])->orderBy('client_name ASC')->with('clientCity')->orderBy($order)->all();
            }else{
                $record = Client::find()->orderBy('client_name ASC')->with('clientCity')->orderBy($order)->all();

            }
         }

        if($asArray){
            foreach ($record as $client){
                $list[$client->client_id] = $client->client_name." ( {$client->clientCity->city_name}, {$client->client_address1} )";
            }
            return $list;
        }

        return $record;
    }

    public static function customerByOutlet(&$id, $cityConcat = false, $addressConcat = false)
    {
        $out = [];
        $models = Client::findBySql("SELECT * FROM `client` WHERE client_id IN (SELECT client_id FROM `sales` WHERE outletId=$id GROUP by client_id ) ")->all();
        foreach ($models as $model){
            if($cityConcat && $addressConcat){
                $out[] = ['id'=>$model->client_id, 'name'=>$model->client_name." ( {$model->clientCity->city_name}, {$model->client_address1} )"];
            }else if($cityConcat){
                $out[] = ['id'=>$model->client_id, 'name'=>$model->client_name." ( {$model->clientCity->city_name} )"];
            }else if($addressConcat){
                $out[] = ['id'=>$model->client_id, 'name'=>$model->client_name." ( {$model->clientCity->client_address1} )"];
            }else{
                $out[] = ['id'=>$model->client_id, 'name'=>$model->client_name];
            }
        }
        return $out;
    }

    public static function &getDuesInvoiceByCustomer(&$customerId)
    {
        $out = [];
        $models = Sales::find()->where("reconciliation_amount+sales_return_amount+received_amount<total_amount-discount_amount AND client_id=".$customerId)->orderBy('sales_id')->all();
        foreach ($models as $model){
            $out[] = ['id'=>$model->sales_id, 'name'=>$model->sales_id];
        }
        return $out;
    }

    public static function getCustomerIdLastPaymentDate($lastDate)
    {
        $customer = [];
        $sql = "SELECT client_id FROM client_payment_history WHERE client_id NOT IN (SELECT client_id FROM client_payment_history WHERE received_at >= '".$lastDate."') GROUP BY client_id";
        $models = ClientPaymentHistory::findBySql($sql)->all();
        foreach($models as $model){
            $customer[] = $model->client_id;
        }
        return $customer;
    }

    public static function getCustomerHasDue($onlyIdsArray = false)
    {
        $customer = [];
        $sql = "SELECT sum( `debit` ) AS debit, sum( `credit` ) AS credit, client_id FROM `customer_account` GROUP BY `client_id`";
        $customerList = CustomerAccount::findBySql($sql)->all();

        foreach($customerList as $model){
            if($model->debit>$model->credit){
                $customer[] = $model->client_id;
            }
        }

        if($onlyIdsArray){
            return $customer;
        }else{
            return Client::findAll($customer);
        }
    }

    /**
     * @param $customerId
     * @param array $InvoiceList
     * @return array
     */
    public static function getDueInvoiceById($customerId, Array $InvoiceList)
    {
        $condition ='';
        foreach($InvoiceList as $invoice){
            $condition.="sales_id=".$invoice." OR ";
        }

        $condition = rtrim($condition, " OR");

        $list = [];
        $sql ="SELECT sum( `debit` ) AS debit, sum( `credit` ) AS credit, sales_id FROM `customer_account` WHERE `client_id` =".$customerId." AND (".$condition.") GROUP BY `sales_id`";
        $customerList = CustomerAccount::findBySql($sql)->all();

        foreach($customerList as $customer) {
            if($customer->debit!=$customer->credit && $customer->debit>$customer->credit){
                $list[] = (object) [
                    'sales_id'=>$customer->sales_id,
                    'memo_id'=>$customer->memo_id,
                    'due'=>$customer->debit-$customer->credit ,
                    'total'=>($customer->sales->total_amount),
                    'less'=>$customer->sales->discount_amount,
                    'received'=>($customer->sales->total_amount) - ($customer->debit-$customer->credit),
                    ];
            }
        }

        return $list;
    }


    /**
     * @param $customerId
     * @return array
     */
    public static function getDueInvoicePrice($customerId)
    {
        $list = [];
        $sql ="SELECT sum( `debit` ) AS debit, sum( `credit` ) AS credit, sales_id FROM `customer_account` WHERE (`client_id` =".$customerId.") GROUP BY `sales_id`";
        $customerList = CustomerAccount::findBySql($sql)->all();

        foreach($customerList as $customer) {
            if($customer->debit!=$customer->credit && $customer->debit>$customer->credit){

                if(!isset($customer->sales)){
                    Utility::debug($customer);
                }

                $list[] =  (object) [
                    'sales_id'=>$customer->sales_id,
                    'memo_id'=>$customer->memo_id,
                    'due'=>$customer->debit-$customer->credit ,
                    'total'=>($customer->sales->total_amount),
                    'less'=>$customer->sales->discount_amount,
                    'received'=>($customer->sales->total_amount) - ($customer->debit-$customer->credit),
                ];
            }
        }

        return $list;
    }

    public static function getInvoiceListByCustomer($customerId)
    {
        $list = [];
        $sql ="SELECT sum( `debit` ) AS debit, sum( `credit` ) AS credit, sales_id FROM `customer_account` WHERE `client_id` =".$customerId." GROUP BY `sales_id`";
        $customerList = CustomerAccount::findBySql($sql)->all();

        foreach($customerList as $customer) {
            if($customer->debit!=$customer->credit && $customer->debit>$customer->credit){
                $list[] = $customer->sales_id ;
            }
        }

        return $list;
    }

    public static function getCityList()
    {
        return City::find()->orderBy('city_name ')->all();
    }

    public static function marketReturnableQty($clientId, $sizeId)
    {

        $returnQty = MarketBook::find()->where([
            'size_id'=>$sizeId,
            'status'=>MarketBook::STATUS_RETURN,
            'client_id'=>$clientId
        ])->sum('quantity');

        $soldQty = MarketBook::find()->where([
            'size_id'=>$sizeId,
            'status'=>MarketBook::STATUS_SELL,
            'client_id'=>$clientId
        ])->sum('quantity');

        return ($soldQty-$returnQty);
    }


}
