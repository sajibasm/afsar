<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\DepositBook;
use app\models\CustomerWithdraw;
use app\models\Expense;
use app\models\Withdraw;
use Yii;

class DepositUtility
{

    public static function getAvailableCash($outletId)
    {
        $openingBalance = self::getOpeningBalance($outletId,'NOW');
        $salesCollection = self::getSalesCollection($outletId,'NOW');
        $dueReceived = self::getDueReceived($outletId,'NOW');
        $cashHandReceived = self::getCashHandReceived($outletId,'NOW');
        $salesReturn = self::getSalesReturn($outletId,'NOW');
        $expense = self::getExpense($outletId,'NOW');
        $withdraw = self::getWithdraw($outletId,'NOW');
        $totalCash = ($openingBalance+$salesCollection+$dueReceived+$cashHandReceived);
        return $totalCash - ($salesReturn+$expense+$withdraw);
    }

    public static function getCashHandReceived($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = DepositBook::find()->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_CASH_HAND_RECEIVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('deposit_in')?$query->sum('deposit_in'):0;
    }


    public static function getClosingBalance($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        //for SalesCollection
        $query = DepositBook::find();
        $query->where(['outletId'=>$outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_SALES]);
        $query->andWhere(['<=', 'created_at',self::getToDate($formattedDate) ]);
        $salesCollection = $query->sum('deposit_in')?$query->sum('deposit_in'):0;

        $query = DepositBook::find();
        $query->where(['outletId'=>$outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_DUE_RECEIVED]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate) ]);
        $dueReceived = $query->sum('deposit_in')?$query->sum('deposit_in'):0;

        //Sales Return
        $query = CustomerWithdraw::find();
        $query->where(['outletId'=>$outletId]);
        $query->andWhere(['=', 'type', CustomerWithdraw::TYPE_BANK]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate) ]);
        $salesReturn = $query->sum('amount')?$query->sum('amount'):0;

        $query = DepositBook::find();
        $query->where(['outletId'=>$outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate) ]);
        $advancedReceived = $query->sum('deposit_in')?$query->sum('deposit_in'):0;


        $query = Expense::find();
        $query->where(['outletId'=>$outletId]);
        $query->andWhere(['=', 'status', Expense::STATUS_APPROVED]);
        $query->andWhere(['=', 'type', Expense::TYPE_DEPOSIT]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate) ]);
        $totalExpense = $query->sum('expense_amount')?$query->sum('expense_amount'):0;

        $query = Withdraw::find();
        $query->where(['outletId'=>$outletId]);
        $query->andWhere(['=', 'status', Withdraw::STATUS_APPROVED]);
        $query->andWhere(['=', 'type', Withdraw::TYPE_DEPOSIT]);
        $query->andWhere(['<=', 'created_at', self::getToDate($formattedDate) ]);
        $withdraw = $query->sum('withdraw_amount')?$query->sum('withdraw_amount'):0;

        $totalCashIn = ($salesCollection+$dueReceived+$advancedReceived);
        $totalCashOut = ($salesReturn+$totalExpense+$withdraw);
        $balance = $totalCashIn - $totalCashOut;
        return $balance;
    }

    public static function getOpeningBalance($outletId, $date = 'NOW')
    {
        $closingDate = DateTimeUtility::getDateIntervalByDate($date, 1 ,'Y-m-d');
        return self::getClosingBalance($outletId, $closingDate);
    }

    public static function getSalesCollection($outletId, $date = 'NOW')
    {

        $formattedDate = DateTimeUtility::getDate($date);
        $query = DepositBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_SALES]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('deposit_in')?$query->sum('deposit_in'):0;
    }

    public static function getAdvanceReceived($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = DepositBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('deposit_in')?$query->sum('deposit_in'):0;
    }

    public static function getDueReceived($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = DepositBook::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'source', DepositBook::SOURCE_DUE_RECEIVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('deposit_in')?$query->sum('deposit_in'):0;
    }

    public static function getSalesReturn($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = CustomerWithdraw::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'type', CustomerWithdraw::TYPE_BANK]);
        $query->andWhere(['=', 'status', CustomerWithdraw::STATUS_APPROVED]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('amount')?$query->sum('amount'):0;
    }

    public static function getExpense($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = Expense::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'status', Expense::STATUS_APPROVED]);
        $query->andWhere(['=', 'type', Expense::TYPE_DEPOSIT]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('expense_amount')?$query->sum('expense_amount'):0;
    }

    public static function getWithdraw($outletId, $date = 'NOW')
    {
        $formattedDate = DateTimeUtility::getDate($date);
        $query = Withdraw::find();
        $query->where(['outletId' => $outletId]);
        $query->andWhere(['=', 'status', Withdraw::STATUS_APPROVED]);
        $query->andWhere(['=', 'type', Withdraw::TYPE_DEPOSIT]);
        $query->andWhere(['between', 'created_at', self::getFromDate($formattedDate), self::getToDate($formattedDate) ]);
        return $query->sum('withdraw_amount')?$query->sum('withdraw_amount'):0;
    }

    private static function getFromDate($date = 'NOW')
    {
        $from = DateTimeUtility::getTodayStartTime();
        if($date!='NOW'){
            $from = $date.' '.DateTimeUtility::getStartTime();
        }
        return $from;
    }

    private static function getToDate($date = 'NOW')
    {
        $to = DateTimeUtility::getTodayEndTime();
        if($date!='NOW'){
            $to = $date.' '.DateTimeUtility::getEndTime();
        }
        return $to;
    }

    public static function analytics($outlet, $date = 'NOW')
    {
        $analytics = self::summery(Utility::decrypt($outlet), $date);
        return [
//                [
//                    "property" => 'Opening',
//                    "numbers" => (int)str_replace(',', '', $analytics['openingBalance'])
//                ],
            [
                "property" => 'Sales Col',
                "numbers" => (int)str_replace(',', '', $analytics['salesCollection'])
            ],
            [
                "property" => 'Due Rec',
                "numbers" => (int)str_replace(',', '', $analytics['dueReceived'])
            ],
            [
                "property" => 'Adv.Rec',
                "numbers" => (int)str_replace(',', '', $analytics['advancedReceived'])
            ],

            [
                "property" => 'Sales Return',
                "numbers" => (int)str_replace(',', '', $analytics['salesReturn'])
            ],
            [
                "property" => 'Expense',
                "numbers" => (int)str_replace(',', '', $analytics['expense'])
            ],
            [
                "property" => 'Withdraw',
                "numbers" => (int)str_replace(',', '', $analytics['withdraw'])
            ],
            [
                "property" => 'Deposit In',
                "numbers" => (int)str_replace(',', '', $analytics['totalDepositIn'])
            ],
            [
                "property" => 'Deposit Out',
                "numbers" => (int)str_replace(',', '', $analytics['totalDepositOut'])
            ],
//                [
//                    "property" => 'Balance',
//                    "numbers" => (int)str_replace(',', '', $analytics['balance'])
//                ]
        ];
    }

    public static function summery($outlet, $date = 'NOW')
    {

        $openingBalance = 0;  self::getOpeningBalance($outlet, $date);

        $salesCollection = self::getSalesCollection($outlet, $date);
        $dueReceived = self::getDueReceived($outlet,$date);
        $advancedReceived = self::getAdvanceReceived($outlet,$date);

        $salesReturn = self::getSalesReturn($outlet,$date);
        $expense = self::getExpense($outlet,$date);
        $withdraw = self::getWithdraw($outlet,$date);

        $totalDepositIn = ($openingBalance+$salesCollection+$dueReceived+$advancedReceived);
        $totalDepositOut = ($salesReturn+$expense+$withdraw);
        $balance = $totalDepositIn - $totalDepositOut;

        return [
            'date'=>DateTimeUtility::getDate($date, SystemSettings::getDateFormat()),
            'openingBalance'=>Yii::$app->formatter->asCurrency($openingBalance),
            'salesCollection'=>Yii::$app->formatter->asCurrency($salesCollection),
            'dueReceived'=>Yii::$app->formatter->asCurrency($dueReceived),
            'advancedReceived'=>Yii::$app->formatter->asCurrency($advancedReceived),
            'salesReturn'=>Yii::$app->formatter->asCurrency($salesReturn),
            'expense'=>Yii::$app->formatter->asCurrency($expense),
            'withdraw'=>Yii::$app->formatter->asCurrency($withdraw),
            'totalDepositIn'=>Yii::$app->formatter->asCurrency($totalDepositIn),
            'totalDepositOut'=>Yii::$app->formatter->asCurrency($totalDepositOut),
            'balance'=>Yii::$app->formatter->asCurrency($balance),
        ];

    }

}