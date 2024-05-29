<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%expense_type}}".
 *
 * @property integer $expense_type_id
 * @property string $expense_type_name
 * @property string $expense_type_mode
 * @property string $expense_type_status
 *
 * @property Expense[] $expenses
 */
class ExpenseType extends \yii\db\ActiveRecord
{

    const TYPE_LC = 1;
    const TYPE_WAREHOUSE = 2;
    const TYPE_SALARY = 3;
    const TYPE_EMPLOYEE_EXPENSE = 4;
    const TYPE_BANK_RECONCILIATION = 5;

    const TYPE_ACTIVE = 'Active';
    const TYPE_INACTIVE = 'Inactive';

    const TYPE_ACTIVE_LABEL = 'Active';
    const TYPE_INACTIVE_LABLE = 'Inactive';

    const EXPENSE_TYPE_MODE_FIXED = 'fixed';
    const EXPENSE_TYPE_MODE_VARIABLE = 'variable';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%expense_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expense_type_name', 'expense_type_status', 'expense_type_mode'], 'string', 'max' => 50],
            [['expense_type_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'expense_type_id' => Yii::t('app', 'ID'),
            'expense_type_name' => Yii::t('app', 'Name'),
            'expense_type_mode' => Yii::t('app', 'Mode'),
            'expense_type_status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(Expense::className(), ['expense_type_id' => 'expense_type_id']);
    }

}
