<?php

namespace app\models;

use app\components\CashUtility;
use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%withdraw}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property double $withdraw_amount
 * @property string $remarks
 * @property string $type
 * @property integer $outletId
 * @property integer $bank_id
 * @property integer $branch_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property Bank $bank
 * @property Branch $branch
 * @property PaymentType $paymentType
 *
 */
class Withdraw extends \yii\db\ActiveRecord
{

    public $type_id;

    const TYPE_DEPOSIT = 'Bank';
    const TYPE_CASH = 'Cash';

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
        return '{{%withdraw}}';
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
            [['withdraw_amount', 'type', 'type_id', 'outletId'], 'required', 'on'=>['create', 'update']],
            [['user_id', 'bank_id', 'branch_id', 'type_id'], 'integer'],
            [['type'], 'string'],
            [['status'], 'string'],
            [['created_at', 'updated_at', 'created_to'], 'safe'],
            [['remarks'], 'string', 'max' => 200],
            [['remarks', 'withdraw_amount'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'withdraw_amount' => Yii::t('app', 'Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'type_id' => Yii::t('app', 'Payment Type'),
            'type' => Yii::t('app', 'Type'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),
            'created_at' => Yii::t('app', 'Date'),
            'status' => Yii::t('app', 'Status'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_to' => Yii::t('app', 'To'),
            'outletId' => Yii::t('app', 'Outlet'),
        ];
    }


    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }


    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }

    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'branch_id']);
    }

    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['type' => 'type']);
    }
}
