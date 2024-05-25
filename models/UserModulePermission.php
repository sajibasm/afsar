<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_module_permission}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $module_id
 * @property int $new CREATE
 * @property int $view READ
 * @property int $list Full View
 * @property int $save UPDATE
 * @property int $remove DELETE
 * @property string|null $added_at
 * @property string|null $added_by
 */
class UserModulePermission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_module_permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'module_id', 'new', 'view', 'list', 'save', 'remove'], 'integer'],
            [['new', 'view', 'list', 'save', 'remove'], 'required'],
            [['added_at'], 'safe'],
            [['added_by'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'module_id' => Yii::t('app', 'Module ID'),
            'new' => Yii::t('app', 'New'),
            'view' => Yii::t('app', 'View'),
            'list' => Yii::t('app', 'List'),
            'save' => Yii::t('app', 'Save'),
            'remove' => Yii::t('app', 'Remove'),
            'added_at' => Yii::t('app', 'Added At'),
            'added_by' => Yii::t('app', 'Added By'),
        ];
    }
}
