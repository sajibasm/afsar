<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_ip_whitelist}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property string $user_agent
 * @property string|null $created_at
 */
class UserIpWhitelist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_ip_whitelist}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'ip_address', 'user_agent'], 'required'],
            [['user_id'], 'integer'],
            [['user_agent'], 'string'],
            [['created_at'], 'safe'],
            [['ip_address'], 'string', 'max' => 45],
            [['user_id', 'ip_address'], 'unique', 'targetAttribute' => ['user_id', 'ip_address']],
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
            'ip_address' => Yii::t('app', 'Ip Address'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }
}
