<?php

namespace app\models;

use app\components\CashUtility;
use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%salary_history}}".
 *
 * @property integer $id
 * @property integer $employee_id
 * @property double $withdraw_amount
 * @property double $remaining_salary
 * @property double $payment_type
 * @property integer $month
 * @property integer $year
 * @property string $remarks
 * @property integer $user_id
 * @property string $extra
 * @property string $status
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Employee[] $employee
 * @property PaymentType $paymentType
 * @property User $user
 */
class SalaryHistory extends \yii\db\ActiveRecord
{

    public $bank_id;
    public $branch_id;


    const  STATUS_PENDING = 'Pending';
    const  STATUS_APPROVED = 'Approved';
    const  STATUS_DECLINED = 'Declined';
    const  STATUS_DELETE = 'Delete';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%salary_history}}';
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
            [['employee_id', 'user_id', 'payment_type'], 'required', 'on'=>'daily'],
            [['employee_id', 'payment_type', 'employee_id', 'remaining_salary', 'withdraw_amount', 'month', 'year', 'remarks', 'user_id'], 'required', 'on'=>'monthlySalary'],

            [['employee_id', 'remaining_salary', 'month', 'year', 'remarks', 'user_id', 'payment_type'], 'required', 'on'=>'monthly'],

//            ['withdraw_amount', function ($attribute, $params)
//            {
//
//                $this->addError($attribute, 'You have entered test');
//                return false;
//
//            }],

            //[['withdraw_amount'], 'compare', 'compareAttribute'=>'remaining_salary', 'operator'=>'<=', 'skipOnEmpty'=>true],
            //['withdraw_amount', 'number', 'max' => CashUtility::getAvailableCash()],

            [['bank_id', 'branch_id', 'payment_type', 'updated_by', 'employee_id', 'month', 'year', 'user_id'], 'integer'],

            [['remaining_salary'], 'number'],

            [['withdraw_amount'], 'number'],

            [['extra'], 'string'],

            [['created_at', 'updated_at'], 'safe'],

            [['status'], 'string'],

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
            'employee_id' => Yii::t('app', 'Employee'),
            'withdraw_amount' => Yii::t('app', 'Advanced'),
            'remaining_salary' => Yii::t('app', 'Paid'),
            'month' => Yii::t('app', 'Month'),
            'year' => Yii::t('app', 'Year'),
            'remarks' => Yii::t('app', 'Remarks'),
            'user_id' => Yii::t('app', 'User ID'),
            'extra' => Yii::t('app', 'Extra'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),

            'payment_type' => Yii::t('app', 'Payment Type'),
            'bank_id' => Yii::t('app', 'Bank'),
            'branch_id' => Yii::t('app', 'Branch'),
        ];
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee_id']);
    }

    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['payment_type_id' => 'payment_type']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

}
