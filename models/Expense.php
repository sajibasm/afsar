<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%expense}}".
 *
 * @property integer $expense_id
 * @property integer $expense_type_id
 * @property string $type
 * @property integer $outletId
 * @property integer $ref_id
 * @property integer $user_id
 * @property integer $updated_by
 * @property double $expense_amount
 * @property string $expense_remarks
 * @property string $status
 * @property string $extra
 * @property string $source
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PaymentType $paymentType
 * @property ExpenseType $expenseType
 * @property User $user
 * @property User $approvedBy
 */
class Expense extends \yii\db\ActiveRecord
{

    const TYPE_DEPOSIT = 'Bank';
    const TYPE_CASH = 'Cash';

    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    const  SOURCE_INTERNAL = 'Internal';
    const  SOURCE_EXTERNAL = 'External';


    public $created_to;


    public $payment_type;
    public $bank_id;
    public $branch_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%expense}}';
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
            [['expense_type_id', 'expense_amount', 'outletId'], 'required'],
            [['expense_type_id', 'user_id','ref_id'], 'integer'],
            [['expense_amount'], 'number'],

            [['branch_id', 'bank_id', 'payment_type'], 'integer'],

            [['type'], 'string', 'max'=>20],
            [['created_at', 'updated_at', 'created_to'], 'safe'],
            [['expense_remarks'], 'string', 'max' => 100],
            [['extra'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'expense_id' => Yii::t('app', 'ID'),
            'expense_type_id' => Yii::t('app', 'Expense Type'),
            'type' => Yii::t('app', 'Payment Type'),
            'outletId' => Yii::t('app', 'Outlet'),
            'ref_id' => Yii::t('app', 'Ref ID'),
            'user_id' => Yii::t('app', 'User'),
            'updated_by' => Yii::t('app', 'Approved By'),
            'expense_amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'extra' => Yii::t('app', 'Extra'),
            'expense_remarks' => Yii::t('app', 'Remarks'),
            'source' => Yii::t('app', 'Source'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),

            'payment_type' => Yii::t('app', 'Payment Type'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),

            'created_to' => Yii::t('app', 'To'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseType()
    {
        return $this->hasOne(ExpenseType::className(), ['expense_type_id' => 'expense_type_id']);
    }


    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['type' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_by']);
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }


}
