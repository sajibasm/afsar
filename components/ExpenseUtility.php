<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;


use app\models\ExpenseType;
use yii\helpers\ArrayHelper;

class ExpenseUtility
{

    public static function getExpenseList($order='expense_type_name', $asArray = false)
    {
        $query = ExpenseType::find();
        $query->where(['expense_type_mode'=>ExpenseType::EXPENSE_TYPE_MODE_VARIABLE]);
        $query->orderBy($order);
        $records = $query->all();

        if($asArray) {
            return ArrayHelper::map($records, 'expense_type_id', 'expense_type_name');
        }

        return $records;
    }

    public static function getExpenseTypeStatus()
    {
        return [ExpenseType::TYPE_ACTIVE=>'Active', ExpenseType::TYPE_INACTIVE=>'Inactive'];
    }



}