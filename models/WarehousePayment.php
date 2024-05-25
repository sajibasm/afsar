<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%warehouse_payment}}".
 *
 * @property integer $id
 * @property integer $outletId
 * @property integer $warehouse_id
 * @property double $payment_amount
 * @property integer $payment_type
 * @property integer $month
 * @property integer $year
 * @property integer $user_id
 * @property integer $updated_by
 * @property string $remarks
 * @property string $status
 * @property string $extra
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Warehouse $warehouse
 * @property User $user
 * @property PaymentType $paymentType
 */
class WarehousePayment extends \yii\db\ActiveRecord
{

    const   STATUS_PENDING = 'Pending';
    const   STATUS_APPROVED = 'Approved';
    const   STATUS_DECLINED = 'Declined';
    const   STATUS_DELETE = 'Delete';

    public $bank_id;
    public $branch_id;
    public $created_to;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%warehouse_payment}}';
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
            [['warehouse_id', 'payment_amount', 'month', 'year', 'payment_type', 'status'], 'required'],
            [['id', 'warehouse_id', 'month', 'year', 'user_id', 'payment_type', 'outletId'], 'integer'],
            [['branch_id', 'bank_id'], 'integer'],
            [['remarks'], 'string', 'max'=>200],
            [['payment_amount'], 'number'],
            [['created_at', 'updated_at', 'created_to'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'outletId' => Yii::t('app', 'Outlet'),
            'warehouse_id' => Yii::t('app', 'Warehouse'),
            'payment_amount' => Yii::t('app', 'Amount'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'month' => Yii::t('app', 'Month'),
            'year' => Yii::t('app', 'Year'),
            'user_id' => Yii::t('app', 'User'),

            'remarks' => Yii::t('app', 'Remarks'),

            'updated_by' => Yii::t('app', 'Approved By'),
            'status' => Yii::t('app', 'Status'),
            'extra' => Yii::t('app', 'Extra'),

            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),

            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),

            'created_to' => Yii::t('app', 'To'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['warehouse_id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['payment_type_id' => 'payment_type']);
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['id' => 'outletId']);
    }
}
