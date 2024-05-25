<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%bank_reconciliation}}".
 *
 * @property integer $id
 * @property integer $outletId
 * @property integer $user_id
 * @property integer $payment_type
 * @property integer $reconciliation_type
 * @property integer $bank_id
 * @property integer $branch_id
 * @property integer $customer_id
 * @property integer $invoice_id
 * @property double $amount
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $status
 *
 *  * @property Client[] $customer
 *  * @property PaymentType $payment
 *  * @property ReconciliationType $reconciliation
 *  * @property User $user
 *  * @property Bank $bank
 *  * @property Brand $branch
 */
class BankReconciliation extends \yii\db\ActiveRecord
{

    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    public $created_to;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%bank_reconciliation}}';
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
            [['payment_type', 'customer_id', 'invoice_id', 'amount', 'remarks', 'reconciliation_type', 'outletId'], 'required'],
            [['user_id', 'bank_id', 'branch_id', 'invoice_id', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['amount', 'remarks'], 'trim'],
            [['created_at', 'updated_at', 'created_to'], 'safe'],
            [['remarks'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Transaction'),
            'outletId' => Yii::t('app', 'Outlet'),
            'user_id' => Yii::t('app', 'User'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'reconciliation_type' => Yii::t('app', 'Reconciliation Type'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),
            'customer_id' => Yii::t('app', 'Customer'),
            'invoice_id' => Yii::t('app', 'Invoice'),
            'amount' => Yii::t('app', 'Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Update By'),
            'sattus' => Yii::t('app', 'Status'),

            'created_to' => Yii::t('app', 'To'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReconciliation()
    {
        return $this->hasOne(ReconciliationType::className(), ['id' => 'reconciliation_type']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(PaymentType::className(), ['payment_type_id' => 'payment_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }
    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'branch_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'customer_id']);
    }


}
