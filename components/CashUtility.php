<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\components;


use app\models\CashBook;
use app\models\CashHandReceived;
use app\models\CustomerWithdraw;
use app\models\Expense;
use app\models\ExpenseType;
use app\models\Outlet;
use app\models\Sales;
use app\models\Withdraw;
use Yii;

class CashUtility
{

    public static function getAvailableCash($outletId)
    {
        $openingBalance = CashUtility::getOpeningBalance($outletId, 'NOW');
        $salesCollection = CashUtility::getSalesPaidAmount($outletId, 'NOW');
        $dueReceived = CashUtility::getCashDueReceived($outletId, 'NOW');
        $cashHandReceived = CashUtility::getCashHandReceived($outletId, 'NOW');
        $salesReturn = CashUtility::getSalesReturn($outletId, 'NOW');
        $expense = CashUtility::getExpense($outletId, 'NOW');
        $withdraw = CashUtility::getWithdraw($outletId, 'NOW');
        $totalCash = ($openingBalance + $salesCollection + $dueReceived + $cashHandReceived);
        return $totalCash - ($salesReturn + $expense + $withdraw);
    }

    public static function getClosingBalance($outletId, $date = 'NOW')
    {

        $formattedDate = DateTimeUtility::getDate($date);

        //for SalesCollection
        $query = CashBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_SALES]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $salesCollection = $query->sum('cash_in') ? $query->sum('cash_in') : 0;

        $query = CashBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_DUE_RECEIVED]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $dueReceived = $query->sum('cash_in') ? $query->sum('cash_in') : 0;

        //Owner Gave Cash Amount.
        $query = CashBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_CASH_HAND_RECEIVED]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $cashHandReceived = $query->sum('cash_in') ? $query->sum('cash_in') : 0;

        $query = CashBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $advancedReceived = $query->sum('cash_in') ? $query->sum('cash_in') : 0;
        //$advancedReceived = $query->createCommand()->rawSql;

        //Sales Return
        $query = CashBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_SALES_WITHDRAW]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $salesReturn = $query->sum('cash_out') ? $query->sum('cash_out') : 0;


        $query = Expense::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'type', Expense::TYPE_CASH]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $totalExpense = $query->sum('expense_amount') ? $query->sum('expense_amount') : 0;

        $query = CashBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_WITHDRAW]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate)]);
        $withdraw = $query->sum('cash_out') ? $query->sum('cash_out') : 0;

        $totalCashIn = ($salesCollection + $dueReceived + $cashHandReceived + $advancedReceived);
        $totalCashOut = ($salesReturn + $totalExpense + $withdraw);
        $balance = $totalCashIn - $totalCashOut;

        return $balance;
    }

    public static function getOpeningBalance($outlet, $date = 'NOW')
    {
        return CashUtility::getClosingBalance($outlet, DateTimeUtility::getDateIntervalByDate($date, 1, 'Y-m-d'));
    }

    public static function getTotalSalesAmount($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = Sales::find()->where(['outletId' => $outletId]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        return $query->sum('total_amount') ? $query->sum('total_amount') : 0;
    }

    public static function getSalesPaidAmount($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = CashBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_SALES]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        return $query->sum('cash_in') ? $query->sum('cash_in') : 0;
    }

    public static function getCashAdvanceReceived($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = CashBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        return $query->sum('cash_in') ? $query->sum('cash_in') : 0;
    }

    public static function getCashDueReceived($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = CashBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_DUE_RECEIVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        //dd($query->createCommand()->getRawSql());
        return $query->sum('cash_in') ? $query->sum('cash_in') : 0;
    }

    public static function getCashHandReceived($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = CashBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_CASH_HAND_RECEIVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        return $query->sum('cash_in') ? $query->sum('cash_in') : 0;
    }

    public static function getSalesReturn($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        //TODO implement total sales return;

        $query = CashBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_SALES_WITHDRAW]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        //return $query->createCommand()->getRawSql();
        return $query->sum('cash_out') ? $query->sum('cash_out') : 0;

//        $query = CustomerWithdraw::find();
//        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
//        return $query->sum('amount')?$query->sum('amount'):0;
    }

    public static function getExpense($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = Expense::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'type', Expense::TYPE_CASH]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        return $query->sum('expense_amount') ? $query->sum('expense_amount') : 0;
    }

    public static function getWithdraw($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
//        $query = Withdraw::find();
//        $query->andWhere(['=', 'type', Withdraw::TYPE_CASH]);
//        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
//        return $query->sum('withdraw_amount')?$query->sum('withdraw_amount'):0;
        $query = CashBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', CashBook::SOURCE_WITHDRAW]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate)]);
        return $query->sum('cash_out') ? $query->sum('cash_out') : 0;

    }

    private static function getFromDate($date = 'NOW')
    {
        $from = DateTimeUtility::getTodayStartTime();
        if ($date != 'NOW') {
            $from = $date . ' ' . DateTimeUtility::getStartTime();
        }
        return $from;
    }

    private static function getToDate($date = 'NOW')
    {
        $to = DateTimeUtility::getTodayEndTime();
        if ($date != 'NOW') {
            $to = $date . ' ' . DateTimeUtility::getEndTime();
        }
        return $to;
    }

    public static function todaySummery($outletId, $date = 'NOW')
    {

        $sales = CashUtility::getTotalSalesAmount($outletId, $date);

        $salesPaid = CashUtility::getSalesPaidAmount($outletId, $date);

        $due = $sales - $salesPaid;

        $dueReceived = CashUtility::getCashDueReceived($outletId, $date);

        $advancedReceived = CashUtility::getCashAdvanceReceived($outletId, $date);

        $cashHandReceived = CashUtility::getCashHandReceived($outletId, $date);

        $salesReturn = CashUtility::getSalesReturn($outletId, $date);

        $expense = CashUtility::getExpense($outletId, $date);

        $withdraw = CashUtility::getWithdraw($outletId, $date);

        $totalCashIn = ($salesPaid + $dueReceived + $cashHandReceived + $advancedReceived);

        $totalCashOut = ($salesReturn + $expense + $withdraw);

        $depositSalesCollection = DepositUtility::getSalesCollection($outletId, $date);
        $depositDueReceived = DepositUtility::getDueReceived($outletId, $date);
        $depositAdvancedReceived = DepositUtility::getAdvanceReceived($date);

        $depositSalesReturn = DepositUtility::getSalesReturn($outletId, $date);
        $depositExpense = DepositUtility::getExpense($outletId, $date);
        $depositWithdraw = DepositUtility::getWithdraw($outletId, $date);

        $totalDepositIn = ($depositSalesCollection + $depositDueReceived + $depositAdvancedReceived);
        $totalDepositOut = ($depositSalesReturn + $depositExpense + $depositWithdraw);


        $response = [
            'date' => DateTimeUtility::getDate($date, SystemSettings::getDateFormat()),
            'sales' => Yii::$app->formatter->asDecimal($sales + $depositSalesCollection),
            'salesPaid' => Yii::$app->formatter->asDecimal($salesPaid),
            'salesDue' => Yii::$app->formatter->asDecimal($due),
            'dueReceived' => Yii::$app->formatter->asDecimal($dueReceived + $depositDueReceived),
            'salesReturn' => Yii::$app->formatter->asDecimal($salesReturn + $depositSalesReturn),
            'cashHand' => Yii::$app->formatter->asDecimal($cashHandReceived),
            'expense' => Yii::$app->formatter->asDecimal($expense + $depositExpense),
            'withdraw' => Yii::$app->formatter->asDecimal($withdraw + $depositWithdraw),
            'cashIn' => Yii::$app->formatter->asDecimal($totalCashIn),
            'cashOut' => Yii::$app->formatter->asDecimal($totalCashOut),
            'bankIn' => Yii::$app->formatter->asDecimal($totalDepositIn),
            'bankOut' => Yii::$app->formatter->asDecimal($totalDepositOut),
        ];

        return $response;

    }

    public static function summery($outlet, $date = 'NOW')
    {

        $openingBalance = CashUtility::getOpeningBalance($outlet, $date);
        $salesCollection = CashUtility::getSalesPaidAmount($outlet, $date);
        $dueReceived = CashUtility::getCashDueReceived($outlet, $date);
        $advancedReceived = CashUtility::getCashAdvanceReceived($outlet, $date);
        $cashHandReceived = CashUtility::getCashHandReceived($outlet, $date);
        $salesReturn = CashUtility::getSalesReturn($outlet, $date);
        $expense = CashUtility::getExpense($outlet, $date);
        $withdraw = CashUtility::getWithdraw($outlet, $date);

        $totalCashIn = ($openingBalance + $salesCollection + $dueReceived + $cashHandReceived + $advancedReceived);
        $totalCashOut = ($salesReturn + $expense + $withdraw);
        $balance = $totalCashIn - $totalCashOut;

        return [
            'date' => DateTimeUtility::getDate($date, SystemSettings::getDateFormat()),
            'openingBalance' => Yii::$app->formatter->asCurrency($openingBalance),
            'salesCollection' => Yii::$app->formatter->asCurrency($salesCollection),
            'dueReceived' => Yii::$app->formatter->asCurrency($dueReceived),
            'advancedReceived' => Yii::$app->formatter->asCurrency($advancedReceived),
            'cashHandReceived' => Yii::$app->formatter->asCurrency($cashHandReceived),
            'salesReturn' => Yii::$app->formatter->asCurrency($salesReturn),
            'expense' => Yii::$app->formatter->asCurrency($expense),
            'withdraw' => Yii::$app->formatter->asCurrency($withdraw),
            'totalCashIn' => Yii::$app->formatter->asCurrency($totalCashIn),
            'totalCashOut' => Yii::$app->formatter->asCurrency($totalCashOut),
            'balance' => Yii::$app->formatter->asCurrency($balance),
        ];

    }

    public static function monthlySalesSummery($outlet)
    {
        $list = [];
        $query = Sales::find();
        $query->select('SUM(paid_amount) paid, SUM(due_amount) due, SUM(discount_amount) discount, SUM(total_amount) total, created_at');
        $query->where(['outletId' => $outlet]);
        $query->andWhere(['between', 'created_at', DateTimeUtility::getDateInterval(30, 'Y-m-d 00:00:00'), DateTimeUtility::getTodayEndTime()]);
        $query->groupBy('DATE(created_at)');
        $data = $query->asArray(true)->all();
        foreach ($data as $d) {
            $list[] = [
                'date' => DateTimeUtility::getDate($d['created_at'], 'd-m-Y'),
                'paid' => (int)$d['paid'],
                'due' => (int)$d['due'],
                'discount' => (int)$d['discount'],
                'total' => (int)$d['total'],
            ];
        }
        return $list;
    }


    public static function dailySalesGrowth($outlet)
    {
        $list = [];
        $query = Sales::find();
        $query->select('SUM(paid_amount) paid, SUM(due_amount) due, SUM(discount_amount) discount, SUM(total_amount) total, created_at');
        $query->where(['outletId' => $outlet]);
        $query->andWhere(['between', 'created_at', DateTimeUtility::getDateInterval(30, 'Y-m-d 00:00:00'), DateTimeUtility::getTodayEndTime()]);
        $query->groupBy('DATE(created_at)');
        //dd($query->createCommand()->getRawSql());
        $data = $query->asArray(true)->all();
        foreach ($data as $d) {
            $list[] = [
                'date' => DateTimeUtility::getDate($d['created_at'], 'Y-m-d 00:00:00'),
                'paid' => (int)$d['paid'],
                'due' => (int)$d['due'],
                //'discount'=>(int)$d['discount'],
                'sales' => (int)$d['total'],
            ];
        }
        return $list;
    }


    public static function expensePie($outlet)
    {
        $list = [];
        $response = [];

        $models =  ExpenseType::find()->all();
        foreach ($models as $model){
            $list[$model->expense_type_id] = $model->expense_type_name;
        }

        $query = Expense::find();
        $query->select('SUM(expense_amount) amount, expense_type_id');
        $query->where(['outletId' => $outlet]);
        $query->andWhere(['between', 'created_at', DateTimeUtility::getTodayStartTime(), DateTimeUtility::getTodayEndTime()]);
        $query->groupBy('expense_type_id');
        $data = $query->asArray(true)->all();
        foreach ($data as $val){
           $response[] =  [
               'property' => $list[$val['expense_type_id']],
               'value' =>(int)$val['amount']
           ];
        }

        return $response;
    }


    public static function salesPie($outlet)
    {
        $query = Sales::find();
        $query->select('SUM(paid_amount) paid, SUM(due_amount) due, SUM(discount_amount) discount, SUM(total_amount) total');
        $query->where(['outletId' => $outlet]);
        $query->andWhere(['between', 'created_at', DateTimeUtility::getTodayStartTime(), DateTimeUtility::getTodayEndTime()]);
        $data = $query->asArray(true)->one();
        return [
            [
                'property' => 'Paid',
                'value' =>(int)$data['paid']
            ],
            [
                'property' => 'Due',
                'value' => (int)$data['due']
            ],
            [
                'property' => 'Discount',
                'value' => (int)$data['discount']
            ],
        ];
    }


    public static function storeWiseSales()
    {
        $list = [];
        $outletList = [];
        $outlets = Outlet::find()->where(['type' => 'Outlet'])->all();
        foreach ($outlets as $outlet) {
            $outletList[$outlet->outletId] = $outlet->name;
        }
        $query = Sales::find();
        $query->select('SUM(total_amount) total, outletId');
        $query->andWhere(['between', 'created_at', DateTimeUtility::getTodayStartTime(), DateTimeUtility::getTodayEndTime()]);
        $query->groupBy('outletId');
        $data = $query->asArray(true)->all();
        foreach ($data as $d) {
            $list[] = [
                'property' => $outletList[$d['outletId']],
                'value' => (int)$d['total'],
            ];
        }
        return $list;
    }


}