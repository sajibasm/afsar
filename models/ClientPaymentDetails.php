<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%client_payment_details}}".
 *
 * @property integer $client_payment_details_id
 * @property integer $sales_id
 * @property integer $client_id
 * @property integer $payment_history_id
 * @property double $paid_amount
 * @property string $payment_type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Sales $sales
 * @property Client $client
 * @property CustomerAccount $customerAccount
 */
class ClientPaymentDetails extends \yii\db\ActiveRecord
{

    public $outletId;

    const PAYMENT_TYPE_FULL ='Full Settled';
    const PAYMENT_TYPE_PARTIAL ='Partial Settled';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_payment_details}}';
    }


    /**
     * @return array
     */
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
            [['sales_id', 'client_id', 'payment_history_id'], 'required'],
            [['sales_id', 'client_id', 'payment_history_id'], 'integer'],
            [['paid_amount'], 'number'],
            [['payment_type'], 'string'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_payment_details_id' => Yii::t('app', 'Client Payment Details ID'),
            'sales_id' => Yii::t('app', 'Invoice'),
            'client_id' => Yii::t('app', 'Client'),
            'payment_history_id' => Yii::t('app', 'Transaction ID'),
            'paid_amount' => Yii::t('app', 'Paid'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientSalesPayment()
    {
        return $this->hasOne(CustomerAccount::className(), ['id' => 'customer_account_id']);
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
    public function getPaymentHistory()
    {
        return $this->hasOne(ClientPaymentHistory::className(), ['client_payment_history_id' => 'payment_history_id']);
    }

}
