<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "NotificationQueue".
 *
 * @property int $id
 * @property string $type
 * @property string $content
 * @property string $extra_params
 * @property string $status
 * @property int $queue
 * @property int $customerId
 * @property string $message
 * @property int $createdBy
 * @property string $createdAt
 * @property string $updatedAt
 */
class NotificationQueue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificationQueue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'content', 'status'], 'string'],
            [['content', 'extra_params', 'queue', 'customerId', 'message', 'createdBy', 'createdAt', 'updatedAt'], 'required'],
            [['queue', 'customerId', 'createdBy'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['extra_params'], 'string', 'max' => 20],
            [['message'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'content' => 'Content',
            'extra_params' => 'Extra Params',
            'status' => 'Status',
            'queue' => 'Queue',
            'customerId' => 'Customer ID',
            'message' => 'Message',
            'createdBy' => 'Created By',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}
