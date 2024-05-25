<?php

namespace app\modules\asm\models;

use Yii;

/**
 * This is the model class for table "{{%modules_action}}".
 *
 * @property int $id
 * @property int $module
 * @property string $code
 * @property string|null $name
 * @property string|null $action
 * @property int|null $active
 * @property int|null $priority
 */
class ModulesAction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%modules_action}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module', 'code'], 'required'],
            [['module', 'active','priority'], 'integer'],
            [['code'], 'string', 'max' => 128],
            [['name', 'action'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'module' => Yii::t('app', 'Module'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'action' => Yii::t('app', 'Action'),
            'priority' => Yii::t('app', 'Priority'),
            'active' => Yii::t('app', 'Active'),
        ];
    }


    public function getModules()
    {
        return $this->hasOne(Modules::className(), ['id' => 'module']);
    }

}
