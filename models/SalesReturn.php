<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sales_return}}".
 *
 * @property integer $sales_return_id
 * @property integer $outletId
 * @property integer $user_id
 * @property integer $sales_id
 * @property integer $client_id
 * @property string $memo_id
 * @property string $client_name
 * @property string $client_mobile
 * @property double $refund_amount
 * @property double $cut_off_amount
 * @property double $total_amount
 * @property string $remarks
 * @property string $type
 * @property integer $payment_history_id
 * @property string $payment_status
 * @property string $status
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property Sales $sales
 * @property Client $customer
 * @property SalesReturnDetails[] $salesReturnDetails
 */
class SalesReturn extends \yii\db\ActiveRecord
{

    const TYPE_RETURN = 'Return';
    const TYPE_SERVICE = 'Service';
    const TYPE_REPAIR = 'Repair';


    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    public $soldDate;
    public $due_amount;
    public $maxRefundAmount;

    public $created_to;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_return}}';
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
            [['sales_id', 'client_id'], 'required', 'on'=>'verify'],
            [['sales_return_id', 'user_id', 'client_id'], 'required', 'on'=>'create'],
            [['sales_return_id', 'user_id', 'client_id', 'payment_history_id', 'updated_by'], 'integer'],
            [['refund_amount', 'cut_off_amount', 'total_amount', 'due_amount', 'maxRefundAmount'], 'number'],
            [['created_at', 'updated_at', 'type', 'soldDate', 'created_to', 'outletId'], 'safe'],
            [['memo_id'], 'string', 'max' => 15],
            [['payment_status'], 'string', 'max' => 100],
            [['status'], 'string'],
            [['client_name', 'remarks'], 'string', 'max' => 100],
            [['client_mobile'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sales_return_id' => Yii::t('app', 'Return ID'),
            'outletId' => Yii::t('app', 'Outlet'),
            'user_id' => Yii::t('app', 'User'),
            'sales_id' => Yii::t('app', 'Invoice'),
            'client_id' => Yii::t('app', 'Customer'),
            'memo_id' => Yii::t('app', 'Memo ID'),
            'client_name' => Yii::t('app', 'Customer Name'),
            'client_mobile' => Yii::t('app', 'Customer Mobile'),
            'refund_amount' => Yii::t('app', 'Refund'),
            'cut_off_amount' => Yii::t('app', 'Adjust'),
            'due_amount' => Yii::t('app', 'Due'),
            'total_amount' => Yii::t('app', 'Total Refund'),
            'remarks' => Yii::t('app', 'Remarks'),
            'type' => Yii::t('app', 'Type'),
            'updated_at' => Yii::t('app', 'Updated'),
            'soldDate' => Yii::t('app', 'Sold Date'),

            'payment_history_id' => Yii::t('app', 'Transaction Id'),
            'payment_status' => Yii::t('app', 'Transaction Status'),

            'status' => Yii::t('app', 'Status'),
            'updated_by' => Yii::t('app', 'Approved By'),

            'created_at' => Yii::t('app', 'Date'),
            'created_to' => Yii::t('app', 'To'),
        ];
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
    public function getSales()
    {
        return $this->hasOne(Sales::className(), ['sales_id' => 'sales_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesReturnDetails()
    {
        return $this->hasMany(SalesReturnDetails::className(), ['sales_return_id' => 'sales_return_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }
}
