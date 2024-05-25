<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sms_gateway".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $apiKey
 * @property string $senderId
 * @property float $balance
 * @property string $updateAt
 * @property int $status
 */
class SmsGateway extends \yii\db\ActiveRecord
{

    const STATUS = [
        1 => 'Active',
        0 => 'Inactive'
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_gateway';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url', 'apiKey', 'senderId', 'balance'], 'required'],
            [['balance'], 'number'],
            [['updateAt'], 'safe'],
            [['status'], 'integer'],
            [['name', 'apiKey'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 255],
            [['senderId'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'apiKey' => 'Api Key',
            'senderId' => 'Sender ID',
            'balance' => 'Balance',
            'updateAt' => 'Update At',
            'status' => 'Status',
        ];
    }

    public static function cache($model=[])
    {
        $cache = Yii::$app->cache;
        if ($cache->exists('sms_gateway')) {
            $cache->delete('sms_gateway');
        }

        if (empty($model)) {
            $model = SmsGateway::findOne(['status' => self::STATUS_ACTIVE]);
        }
        $cache->add('sms_gateway', $model);
    }
}
