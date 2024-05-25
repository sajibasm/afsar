<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%lc_payment}}".
 *
 * @property integer $lc_payment_id
 * @property integer $lc_id
 * @property integer $lc_payment_type
 * @property integer $user_id
 * @property integer $updated_by
 * @property double $amount
 * @property integer $payment_type
 * @property string $remarks
 * @property string $status
 * @property string $extra
 * @property string $created_at
 * @property string $updated_at
 *
 * @property LcPaymentType $lcPaymentType
 * @property Lc $lc
 * @property User $user
 * @property PaymentType $paymentType
 */
class LcPayment extends \yii\db\ActiveRecord
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
        return '{{%lc_payment}}';
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
            [['lc_payment_type', 'user_id', 'lc_id', 'amount'], 'required'],
            [['lc_payment_id', 'lc_id', 'lc_payment_type', 'user_id'], 'integer'],
            [['amount'], 'number'],
            [['bank_id', 'branch_id', 'payment_type'], 'integer'],
            [['created_at', 'updated_at', 'created_to'], 'safe'],
            [['remarks'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lc_payment_id' => Yii::t('app', 'Payment ID'),
            'lc_id' => Yii::t('app', 'LC'),
            'lc_payment_type' => Yii::t('app', 'Type'),
            'user_id' => Yii::t('app', 'User'),
            'amount' => Yii::t('app', 'Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),

            'updated_by' => Yii::t('app', 'Approved By'),
            'status' => Yii::t('app', 'Status'),
            'extra' => Yii::t('app', 'Extra'),

            'payment_type' => Yii::t('app', 'Payment Type'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),
            'created_to' => Yii::t('app', 'To'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcPaymentType()
    {
        return $this->hasOne(LcPaymentType::className(), ['lc_payment_type_id' => 'lc_payment_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLc()
    {
        return $this->hasOne(Lc::className(), ['lc_id' => 'lc_id']);
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
}
