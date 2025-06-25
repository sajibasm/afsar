<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_login_logs".
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property string|null $city
 * @property string|null $region
 * @property string|null $country
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $country_flag
 * @property string|null $created_at
 * @property string|null $user_agent
 */
class UserLoginLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_login_logs';
    }

    public function beforeSave($insert)
    {
        foreach ($this->attributes as $attribute => $value) {
            if (is_string($value)) {
                $this->$attribute = trim($value);
            }
        }
        return parent::beforeSave($insert);
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'ip_address'], 'required'],
            [['user_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['created_at'], 'safe'],
            [['user_agent'], 'string'],
            [['ip_address'], 'string', 'max' => 45],
            [['city', 'region', 'country'], 'string', 'max' => 100],
            [['country_flag'], 'string', 'max' => 255],
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
            'city' => Yii::t('app', 'City'),
            'region' => Yii::t('app', 'Region'),
            'country' => Yii::t('app', 'Country'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'country_flag' => Yii::t('app', 'Country Flag'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_agent' => Yii::t('app', 'User Agent'),
        ];
    }
}
