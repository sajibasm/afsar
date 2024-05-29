<?php

namespace app\models;

use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%client_payment_history}}".
 *
 * @property integer $client_payment_history_id
 * @property integer $outletId
 * @property integer $client_id
 * @property integer $user_id
 * @property integer $updated_by
 * @property double $received_amount
 * @property double $remaining_amount
 * @property string $remarks
 * @property string $received_type
 * @property string $extra
 * @property integer $payment_type_id
 * @property string $received_at
 * @property string $updated_at
 * @property string $status
 *
 * @property ClientPaymentDetails[] $clientPaymentDetails
 * @property Client $customer
 * @property User $user
 * @property PaymentType $paymentType
 */
class ClientPaymentHistory extends \yii\db\ActiveRecord
{

    const  RECEIVED_TYPE_DUE_RECEIVED = 'Due-Received';
    const  RECEIVED_TYPE_SALES_UPDATE = 'Sales-Update';
    const  RECEIVED_TYPE_SALES_RETURN = 'Sales-Return';
    const  RECEIVED_TYPE_ADVANCED = 'Advanced';

    const PAY_TYPE_AUTO = 'Auto';
    const PAY_TYPE_MANUAL = 'Manual';

    const  PAYMENT_TYPE_SALES_RETURN_ID = 3;
    const  PAYMENT_TYPE_SALES_UPDATE_ID = 4;

    const  PAYMENT_TYPE_CASH_ID = 1;
    const  PAYMENT_TYPE_BANK_ID = 2;
    const  PAYMENT_TYPE_ONLINE_ID = 5;


    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';
    const  STATUS_Hold= 'Hold';


    public $invoices;
    public $payType;

    public $bank_id;
    public $branch_id;

    public $source;
    public $name;

    public $email;
    public $sms;

    public $customerWithdrawId = null;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_payment_history}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'received_at',
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
            [['client_id', 'received_amount', 'payment_type_id'], 'required', 'on'=>'add'],

            [['payType'], 'required', 'on'=>['payMode']],
            [['payment_type_id', 'remarks'], 'required', 'on'=>['withdrawMode']],

            [['invoices', 'branch_id', 'source', 'bank_id', 'outletId'], 'safe'],
            [['client_id'], 'required'],
            [['client_id', 'user_id', 'payment_type_id'], 'integer'],
            [['received_amount', 'remaining_amount'], 'number'],
            [['received_at', 'updated_at'], 'safe'],
            [['remarks'], 'string', 'max' => 300],
            [['extra'], 'string']
        ];
    }

    public function beforeSave($insert)
    {
        if(empty($this->extra)) {
            $this->extra =  Json::encode(["PaymentType"=>"N/A", "Bank"=>null, "Branch"=>null]);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_payment_history_id' => Yii::t('app', 'Transaction ID'),
            'outletId' => Yii::t('app', 'Outlet'),
            'payType' => Yii::t('app', 'Method'),
            'client_id' => Yii::t('app', 'Customer'),
            'user_id' => Yii::t('app', 'Created'),
            'invoices' => Yii::t('app', 'Invoice'),
            'received_amount' => Yii::t('app', 'Received'),
            'remaining_amount' => Yii::t('app', 'Available Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'payment_type_id' => Yii::t('app', 'Payment Type'),
            'extra' => Yii::t('app', 'Extra'),
            'received_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated'),
            'source' => Yii::t('app', 'Received Type'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),
            'email' => Yii::t('app', 'Email'),
            'SMS' => Yii::t('app', 'SMS'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentDetails()
    {
        return $this->hasMany(ClientPaymentDetails::className(), ['payment_history_id' => 'client_payment_history_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['payment_type_id' => 'payment_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }

    public function getPaymentMode()
    {
        return [self::PAY_TYPE_AUTO=>'Auto', self::PAY_TYPE_MANUAL=>'Invoice'];
    }

    public static function getFormReceivedType()
    {
        return [
            self::RECEIVED_TYPE_ADVANCED=>self::RECEIVED_TYPE_ADVANCED,
            self::RECEIVED_TYPE_DUE_RECEIVED=>self::RECEIVED_TYPE_DUE_RECEIVED,
        ];
    }

    public static function getPaymentReceivedType()
    {
        $list = [];
        $models = ClientPaymentHistory::find()->distinct('received_type')->all();
        foreach ($models as $model){
            $list[$model->received_type] = $model->received_type;
        }
        return $list;
    }

    public static function getReceivedType()
    {
        return [
            self::RECEIVED_TYPE_ADVANCED=>self::RECEIVED_TYPE_ADVANCED,
            self::RECEIVED_TYPE_DUE_RECEIVED=>self::RECEIVED_TYPE_DUE_RECEIVED,
            self::RECEIVED_TYPE_SALES_RETURN=>self::RECEIVED_TYPE_SALES_RETURN,
            self::RECEIVED_TYPE_SALES_UPDATE=>self::RECEIVED_TYPE_SALES_UPDATE,
        ];
    }

    public static function getStatusList()
    {
        [
            self::STATUS_PENDING=>self::STATUS_PENDING,
            self::STATUS_APPROVED=>self::STATUS_APPROVED,
            self::STATUS_DECLINED=>self::STATUS_DECLINED,
        ];
    }

}
