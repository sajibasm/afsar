<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%product_unit}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $status
 */
class ProductUnit extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_unit}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 50],
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
}
