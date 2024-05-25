<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_role}}".
 *
 * @property integer $user_role_id
 * @property string $user_role_name
 * @property string $user_role_status
 *
 * @property User[] $users
 */
class UserRole extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_role_id'], 'required'],
            [['user_role_id'], 'integer'],
            [['user_role_status'], 'string'],
            [['user_role_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_role_id' => Yii::t('app', 'User Role ID'),
            'user_role_name' => Yii::t('app', 'User Role Name'),
            'user_role_status' => Yii::t('app', 'User Role Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['user_role_id' => 'user_role_id']);
    }
}
