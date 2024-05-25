<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\components;


use app\models\Employee;
use app\models\EmployeeDesignation;
use app\models\ExpenseType;
use app\models\SalaryHistory;
use yii\helpers\ArrayHelper;

class EmployeeUtility
{

    public static function getEmployeeList($order = 'full_name', $status = Employee::ACTIVE_STATUS, $asArray = false)
    {
        $records = Employee::find()->where(['status' => $status])->orderBy($order)->all();

        if ($asArray) {
            return ArrayHelper::map($records, 'id', 'full_name');
        }

        return $records;
    }

    public static function getEmployeeStatus()
    {
        return [Employee::ACTIVE_STATUS => Employee::ACTIVE_STATUS_LABEL, Employee::INACTIVE_STATUS => Employee::INACTIVE_STATUS_LABEL];
    }

    public static function getEmployeeDesignationList($status = EmployeeDesignation::ACTIVE_STATUS, $order = 'name')
    {
        return EmployeeDesignation::find()->where(['status' => $status])->orderBy($order)->all();
    }

    public static function getEndDateOfDOB()
    {
        $date = new \DateTime('now');
        $date->modify('-15 year');
        return $date->format('Y-m-d');
    }

    public static function getRemainingSalary($employeeId, $month, $year)
    {
        $employee = Employee::findOne($employeeId);
        $salaryHistory = SalaryHistory::find()->select('SUM(withdraw_amount) as withdraw_amount')->where(['employee_id' => $employeeId, 'month' => $month, 'year' => $year])->orderBy('id DESC')->one();
        return [
            'paid' => (float)$salaryHistory->withdraw_amount,
            'remaining' => (float)($employee->salary - $salaryHistory->withdraw_amount),
            'salary' => (float)$employee->salary,
        ];
    }

}