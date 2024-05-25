<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%deposit_book}}".
 *
 * @property integer $id
 * @property integer $outletId
 * @property integer $bank_id
 * @property integer $branch_id
 * @property integer $payment_type_id
 * @property double $ref_user_id
 * @property double $deposit_in
 * @property double $deposit_out
 * @property integer $reference_id
 * @property string $source
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Bank $bank
 * @property Branch $branch
 * @property Client $customer
 * @property ClientPaymentHistory $clientPaymentHistory
 * @property PaymentType $paymentType
 */
class DepositBook extends \yii\db\ActiveRecord
{

    const SOURCE_SALES = 'Sales';
    const SOURCE_SALES_UPDATE = 'Sales Update';
    const SOURCE_DUE_RECEIVED = 'Due Received';
    const SOURCE_DUE_RECEIVED_OVERFLOW = 'Due Received';
    const SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED = 'Advance Customer Payment';
    const SOURCE_CASH_HAND_RECEIVED = 'Cash Hand Received';
    const SOURCE_ADVANCE_SALES = 'Advance Sales';
    const SOURCE_WITHDRAW = 'Withdraw';
    const SOURCE_SALES_WITHDRAW = 'Sales Withdraw';


    const SOURCE_EMPLOYEE_ADV_SALARY = 'Advance Salary';
    const SOURCE_LC = 'LC';
    const SOURCE_WAREHOUSE = 'Warehouse';
    const SOURCE_DAILY_EXPENSE = 'Daily Expense';
    const SOURCE_RECONCILIATION = 'Reconciliation';

    const TYPE_FILTER_INFLOW = 'Inflow';
    const TYPE_FILTER_OUTFLOW = 'Outflow';

    public $typeFilter;
    public $amountFrom;
    public $amountTo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%deposit_book}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
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
            [['outletId', 'created_at'], 'required', 'on'=>['Summery']],
            [['deposit_in', 'deposit_out'], 'number'],
            [['source'], 'string'],
            [['created_at', 'updated_at', 'outletId'], 'safe'],
            [['reference_id', 'bank_id' ,'branch_id', 'ref_user_id', 'payment_type_id', 'amountFrom', 'amountTo', 'typeFilter'], 'integer'],
            [['remarks'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),
            'payment_type_id' => Yii::t('app', 'Payment Type'),
            'ref_user_id' => Yii::t('app', 'Ref User'),
            'deposit_in' => Yii::t('app', 'Received'),
            'deposit_out' => Yii::t('app', 'Spent'),
            'reference_id' => Yii::t('app', 'Ref.'),
            'source' => Yii::t('app', 'Source'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),

            'typeFilter' => Yii::t('app', 'Type'),
            'amountFrom' => Yii::t('app', 'Amount From'),
            'amountTo' => Yii::t('app', 'Amount To'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentHistory()
    {
        return $this->hasOne(ClientPaymentHistory::className(), ['client_payment_history_id' => 'reference_id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     * @deprecated
     */
    public function getCustomer()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'branch_id']);
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['payment_type_id' => 'payment_type_id']);
    }


    public static function getTypeFilterList()
    {
        return [
            'Inflow'=>'Inflow',
            'Outflow'=>'Outflow',
        ];
    }

}
