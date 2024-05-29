<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%customer_account}}".
 *
 * @property integer $id
 * @property integer $sales_id
 * @property string $memo_id
 * @property integer $client_id
 * @property string $type
 * @property string $payment_type
 * @property integer $payment_history_id
 * @property string $account
 * @property double $debit
 * @property double $credit
 * @property double $balance
 * @property string $date
 *
 * @property Sales $sales
 * @property Client $client
 */
class CustomerAccount extends \yii\db\ActiveRecord
{
    const  TYPE_SALES = 'Sales';
    const  TYPE_RECONCILIATION = 'Reconciliation';
    const  TYPE_RETURN = 'Return';
    const  TYPE_REPAIR = 'Repair';
    const  TYPE_SERVICE = 'Service';

    const  PAYMENT_TYPE_BANK = 'Bank';
    const  PAYMENT_TYPE_CASH = 'Cash';
    const  PAYMENT_TYPE_NA = 'N/A';

    const  ACCOUNT_RECEIVABLE = 'Account Receivable';
    const  ACCOUNT_RECEIVABLE_UPDATE = 'Account Receivable(Update)';
    const  ACCOUNT_RECONCILIATION = 'Account Reconciliation';
    const  ACCOUNT_SALES = 'Sales';
    const  ACCOUNT_SALES_UPDATE = 'Sales(Update)';
    const  ACCOUNT_SALES_WITHDRAW = 'Sales(Withdraw)';
    const  ACCOUNT_DUE_RECEIVED = 'Due-Received';
    const  ACCOUNT_DUE_RECEIVED_RESTORE = 'Due-Received-Restore';
    const  ACCOUNT_SALES_RETURN = 'Sales Return';
    const  ACCOUNT_SALES_REPAIR = 'Sales Repair';
    const  ACCOUNT_ACCOUNT_DEPOSIT = 'Sales Return (Account Deposit)';

    public $paid_amount;
    public $due_amount;
    public $discount_amount;
    public $total_amount;
    public $totalDues;

    public $outletId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer_account}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date',
                'updatedAtAttribute' => 'date',
                'value' => function() { return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'); }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_id', 'client_id'], 'integer'],
            [['type', 'account'], 'string'],
            [['debit', 'credit', 'balance'], 'number'],
            [['date'], 'safe'],
            [['memo_id'], 'string', 'max' => 15],
            [['account'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sales_id' => Yii::t('app', 'Invoice'),
            'memo_id' => Yii::t('app', 'Memo No'),
            'client_id' => Yii::t('app', 'Customer'),
            'type' => Yii::t('app', 'Type'),
            'account' => Yii::t('app', 'Account'),
            'debit' => Yii::t('app', 'Debit'),
            'credit' => Yii::t('app', 'Credit'),
            'balance' => Yii::t('app', 'Balance'),
            'date' => Yii::t('app', 'Date'),

            'payment_history_id' => Yii::t('app', 'Payment Id'),

            'paid_amount' => Yii::t('app', 'Paid'),
            'due_amount' => Yii::t('app', 'Due'),
            'discount_amount' => Yii::t('app', 'Less'),
            'total_amount' => Yii::t('app', 'Total'),

            //required for filter options.
            'fromDate' => Yii::t('app', 'Date'),
            'toDate' => Yii::t('app', 'Date To'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasOne(Sales::className(), ['sales_id' => 'sales_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentHistory()
    {
        return $this->hasOne(ClientPaymentHistory::className(), ['client_id' => 'client_id'])->orderBy('client_payment_history_id DESC');
    }


}
