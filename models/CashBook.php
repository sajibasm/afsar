<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%cash_book}}".
 *
 * @property integer $id
 * @property double $cash_in
 * @property double $cash_out
 * @property string $source
 * @property integer $ref_user_id
 * @property integer $reference_id
 * @property string $remarks
 * @property string $outletId
 * @property string $created_at
 * @property string $updated_at
 */
class CashBook extends \yii\db\ActiveRecord
{
    const SOURCE_SALES = 'Sales';
    const SOURCE_SALES_UPDATE = 'Sales Update';
    const SOURCE_DUE_RECEIVED = 'Due Received';
    const SOURCE_DUE_RECEIVED_OVERFLOW = 'Due Received';
    const SOURCE_ADVANCE_CUSTOMER_PAYMENT_RECEIVED = 'Advance Customer Payment';
    const SOURCE_CASH_HAND_RECEIVED = 'Cash Hand Received';
    const SOURCE_ADVANCE_SALES = 'Advance Sales';
    const SOURCE_WITHDRAW = 'Withdraw';
    const SOURCE_SALES_WITHDRAW = 'Sales Withdraw';

    const SOURCE_EMPLOYEE_ADV_SALARY = 'Advance Salary';
    const SOURCE_LC = 'LC';
    const SOURCE_WAREHOUSE = 'Warehouse';
    const SOURCE_DAILY_EXPENSE = 'Daily Expense';
    const SOURCE_RECONCILIATION = 'Reconciliation';

    const TYPE_FILTER_INFLOW = 'Inflow';
    const TYPE_FILTER_OUTFLOW = 'Outflow';

    public $typeFilter = null;
    public $amountFrom = null;
    public $amountTo = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cash_book}}';
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
            [['outletId', 'created_at'], 'required', 'on'=>['Summery']],
            [['cash_in', 'cash_out'], 'number'],
            [['reference_id', 'ref_user_id', 'outletId'], 'integer'],
            [['source'], 'string'],
            [['created_at', 'updated_at', 'typeFilter', 'outletId'], 'safe'],
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
            'cash_in' => Yii::t('app', 'Inflow'),
            'cash_out' => Yii::t('app', 'Outflow'),
            'source' => Yii::t('app', 'Source'),
            'ref_user_id' => Yii::t('app', 'User'),
            'reference_id' => Yii::t('app', 'Source ID'),
            'remarks' => Yii::t('app', 'Remarks'),
            'outletId' => Yii::t('app', 'Outlet'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated'),

            'typeFilter' => Yii::t('app', 'Type'),
            'typeFilter' => Yii::t('app', 'Type'),
        ];
    }


    public static function getTypeFilterList()
    {
        return [
            'Inflow'=>'Inflow',
            'Outflow'=>'Outflow',
        ];
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outletId']);
    }

}
