<?php

namespace app\models;
use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "{{%sales}}".
 *
 * @property integer $sales_id
 * @property string $memo_id
 * @property integer $client_id
 * @property string $client_name
 * @property string $client_mobile
 * @property integer $client_type
 * @property integer $user_id
 * @property integer $updated_by
 * @property string $contact_number
 * @property double $received_amount
 * @property double $reconciliation_amount
 * @property double $sales_return_amount
 * @property double $paid_amount
 * @property double $due_amount
 * @property double $discount_amount
 * @property double $total_amount
 * @property string $remarks
 * @property string $type
 * @property integer $payment_type
 * @property integer $bank
 * @property integer $branch
 * @property integer $transport_id
 * @property integer $outletId
 * @property string $transport_name
 * @property string $tracking_number
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Challan $challans
 * @property ClientPaymentDetails $clientPaymentDetails
 * @property Client $client
 * @property User $user
 * @property Transport $transport
 * @property User $verified
 * @property SalesDetails $salesDetails
 * @property PaymentType $paymentTypeModel
 */
class Sales extends \yii\db\ActiveRecord
{

    public $email;
    public $sms;
    public $reconciliationAmount = 0;
    public $isReport = false;

    const CUSTOMER_TYPE_REGULAR = 1;
    const CUSTOMER_TYPE_IRREGULAR = 2;

    const  TYPE_SALES = 'sales';
    const  TYPE_RETURN = 'return';
    const  TYPE_SALES_UPDATE = 'sales-update';

    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    public $created_to;

    //For Search Reports Type: Full Paid, Due
    public $invoiceType;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales}}';
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

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outletId'], 'required', 'on'=>['outlet']],
            [['client_id', 'user_id', 'paid_amount', 'due_amount', 'discount_amount', 'total_amount', 'payment_type', 'status'], 'required', 'on'=>['Sales']],
            [['client_id', 'client_type', 'user_id', 'payment_type', 'bank', 'branch', 'updated_by', 'outletId'], 'integer'],
            [['paid_amount', 'due_amount', 'discount_amount', 'total_amount', 'reconciliation_amount', 'sales_return_amount', 'received_amount'], 'number'],
            [['client_name', 'remarks'], 'string', 'max' => 100],
            [['type', 'client_mobile'], 'string', 'max' => 20],
            [['memo_id'], 'string', 'max' => 15],
            [['contact_number'], 'string', 'max' => 20],

            [['created_at', 'updated_at', 'created_to'], 'safe'],

            [['paid_amount', 'discount_amount', 'total_amount'], 'number', 'min' => 0],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sales_id' => Yii::t('app', 'Invoice'),
            'memo_id' => Yii::t('app', 'Memo No'),
            'client_id' => Yii::t('app', 'Customer'),
            'client_name' => Yii::t('app', 'Customer'),
            'client_type' => Yii::t('app', 'Type'),
            'user_id' => Yii::t('app', 'User'),
            'update_by' => Yii::t('app', 'Approved'),
            'contact_number' => Yii::t('app', 'Mobile'),
            'received_amount' => Yii::t('app', 'Total Paid'),
            'reconciliation_amount' => Yii::t('app', 'Reconciliation'),
            'sales_return_amount' => Yii::t('app', 'Return'),
            'paid_amount' => Yii::t('app', 'Paid'),
            'due_amount' => Yii::t('app', 'Dues'),
            'discount_amount' => Yii::t('app', 'Less'),
            'total_amount' => Yii::t('app', 'Total'),
            'remarks' => Yii::t('app', 'Remarks'),
            'type' => Yii::t('app', 'Type'),
            'payment_type' => Yii::t('app', 'Type'),
            'bank' => Yii::t('app', 'Bank'),
            'branch' => Yii::t('app', 'Branch'),

            'transport_id' => Yii::t('app', 'Transport'),

            'outletId' => Yii::t('app', 'Outlet'),

            'transport_name' => Yii::t('app', 'Transport'),
            'tracking_number' => Yii::t('app', 'Tracking Number'),

            'email' => Yii::t('app', 'Email'),
            'sms' => Yii::t('app', 'SMS'),

            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Verified By'),

            'created_to' => Yii::t('app', 'To'),

            'invoiceType' => Yii::t('app', 'Status'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallans()
    {
        return $this->hasMany(Challan::className(), ['sales_id' => 'sales_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentDetails()
    {
        return $this->hasMany(ClientPaymentDetails::className(), ['sales_id' => 'sales_id']);
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
    public function getVerified()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorized()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['transport_id' => 'transport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::className(), ['sales_id' => 'sales_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentTypeModel()
    {
        return $this->hasOne(PaymentType::className(), ['payment_type_id' => 'payment_type']);
    }
}
