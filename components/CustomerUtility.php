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
use app\models\ClientFinancialSummary;
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
        $summary = ClientFinancialSummary::findOne(['client_id' => $customerId]);
        return $summary ->total_due ?? 0;
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

    public static function findCustomersWithAddresses($type = null, $order = 'client_name', $asArray = false, $outlet = null)
    {
        $query = Client::find()
            ->with('clientCity')
            ->orderBy($order);

        if (!empty($type)) {
            $query->andWhere(['client_type' => $type]);
        }

        if (!empty($outlet)) {
            $query->andWhere(['outletId' => $outlet]);
        }

        $clients = $query->all();

        if ($asArray) {
            $list = [];
            foreach ($clients as $client) {
                $cityName = $client->clientCity->city_name ?? '';
                $address = $client->client_address1 ?? '';
                $list[$client->client_id] = "{$client->client_name} ({$cityName}, {$address})";
            }
            return $list;
        }

        return $clients;
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

    public static function getDuesInvoiceByCustomer(&$customerId)
    {
        $out = [];
        $models = Sales::find()->where("reconciliation_amount+sales_return_amount+received_amount<total_amount-discount_amount AND client_id=".$customerId)->orderBy('sales_id')->all();
        foreach ($models as $model){
            $out[] = ['id'=>$model->sales_id, 'name'=>$model->sales_id];
        }
        return $out;
    }

    public static function getLastPaymentDataByCustomerId($lastDate)
    {
        $customer = [];
        $sql = "SELECT client_id FROM client_payment_history WHERE client_id NOT IN (SELECT client_id FROM client_payment_history WHERE received_at >= '".$lastDate."') GROUP BY client_id";
        $models = ClientPaymentHistory::findBySql($sql)->all();
        foreach($models as $model){
            $customer[] = $model->client_id;
        }
        return $customer;
    }

    public static function hasCustomerDue($onlyIdsArray = false)
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
    public static function getDueInvoicesByCustomer($customerId): array
    {
        if (empty($customerId)) {
            return [];
        }

        $salesList = Sales::find()
            ->where(['client_id' => $customerId])
            ->all();

        return array_values(array_filter(array_map(function ($sale) {
            $netPayable = $sale->total_amount - $sale->discount_amount;
            $totalReceived = $sale->paid_amount + $sale->reconciliation_amount + $sale->sales_return_amount;
            $remainingDue = $netPayable - $totalReceived;

            if ($remainingDue <= 0) {
                return null;
            }

            return (object) [
                'sales_id' => $sale->sales_id,
                'memo_id' => $sale->memo_id ?? null,
                'due' => $remainingDue,
                'total' => $sale->total_amount,
                'less' => $sale->discount_amount,
                'received' => $totalReceived,
            ];
        }, $salesList)));
    }

    public static function getDueInvoicesById(array $invoiceIds): array
    {
        if (empty($invoiceIds)) {
            return [];
        }

        $salesList = Sales::find()
            ->andWhere(['in', 'sales_id', $invoiceIds])
            ->all();

        return array_values(array_filter(array_map(function ($sale) {
            $netPayable = $sale->total_amount - $sale->discount_amount;
            $totalReceived = $sale->paid_amount + $sale->reconciliation_amount + $sale->sales_return_amount;
            $remainingDue = $netPayable - $totalReceived;

            if ($remainingDue <= 0) {
                return null;
            }

            return (object) [
                'sales_id' => $sale->sales_id,
                'memo_id' => $sale->memo_id ?? null,
                'due' => $remainingDue,
                'total' => $sale->total_amount,
                'less' => $sale->discount_amount,
                'received' => $totalReceived,
            ];
        }, $salesList)));
    }


    public static function findDueInvoiceIdsByCustomer($customerId): array
    {
        $salesList = Sales::find()
            ->select([
                'sales_id',
                'total_amount',
                'discount_amount',
                'paid_amount',
                'reconciliation_amount',
                'sales_return_amount'
            ])
            ->where(['client_id' => $customerId])
            ->all();

        return array_values(array_filter(array_map(function ($sale) {
            $netPayable = $sale->total_amount - $sale->discount_amount;
            $totalReceived = $sale->paid_amount + $sale->reconciliation_amount + $sale->sales_return_amount;
            $remainingDue = $netPayable - $totalReceived;

            return $remainingDue > 0 ? $sale->sales_id : null;
        }, $salesList)));
    }

    public static function getAllCities()
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
