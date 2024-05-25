<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%modules_list}}".
 *
 * @property int $module_id
 * @property string|null $module_name
 * @property string $controller
 * @property string $icon
 * @property int|null $is_active
 */
class ModulesList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%modules_list}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller', 'icon'], 'required'],
            [['is_active'], 'integer'],
            [['module_name'], 'string', 'max' => 100],
            [['controller'], 'string', 'max' => 50],
            [['icon'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'module_id' => Yii::t('app', 'Module ID'),
            'module_name' => Yii::t('app', 'Module Name'),
            'controller' => Yii::t('app', 'Controller'),
            'icon' => Yii::t('app', 'Icon'),
            'is_active' => Yii::t('app', 'Is Active'),
        ];
    }
}
