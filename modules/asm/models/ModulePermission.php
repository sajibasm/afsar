<?php

namespace app\modules\asm\models;

use app\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%module_permission}}".
 *
 * @property int $id
 * @property string $code
 * @property int|null $userId
 * @property int|null $module
 * @property int $module_action_id
 * @property string|null $createdAt
 * @property int|null $createdBy
 */
class ModulePermission extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%module_permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'module_action_id'], 'required'],
            [['userId', 'module', 'module_action_id', 'createdBy'], 'integer'],
            [['createdAt'], 'safe'],
            [['code'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'userId' => Yii::t('app', 'User ID'),
            'module' => Yii::t('app', 'Module'),
            'module_action_id' => Yii::t('app', 'Module Action ID'),
            'createdAt' => Yii::t('app', 'Created At'),
            'createdBy' => Yii::t('app', 'Created By'),
        ];
    }

    public function getModules()
    {
        return $this->hasOne(Modules::className(), ['id' => 'module']);
    }

    public function getAction()
    {
        return $this->hasOne(ModulesAction::className(), ['id' => 'module_action_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'userId']);
    }


}
