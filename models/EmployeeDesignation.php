<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%employee_designation}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 *
 * @property Employee[] $employees
 */
class EmployeeDesignation extends \yii\db\ActiveRecord
{

    const  ACTIVE_STATUS = 1;
    const  INACTIVE_STATUS = 0;

    const  ACTIVE_STATUS_LABEL = 'Active';
    const  INACTIVE_STATUS_LABEL = 'Inactive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%employee_designation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['designation' => 'id']);
    }
}
