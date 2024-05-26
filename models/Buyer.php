<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%buyer}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $city
 * @property string $address1
 * @property string $address2
 * @property string $contact_number
 * @property string $contact_person
 * @property string $contact_person_number
 */
class Buyer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%buyer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city'], 'required'],
            [['city'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['address1', 'address2', 'contact_person'], 'string', 'max' => 50],
            [['contact_number', 'contact_person_number'], 'string', 'max' => 20]
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
            'city' => Yii::t('app', 'City'),
            'address1' => Yii::t('app', 'Address1'),
            'address2' => Yii::t('app', 'Address2'),
            'contact_number' => Yii::t('app', 'Contact Number'),
            'contact_person' => Yii::t('app', 'Contact Person'),
            'contact_person_number' => Yii::t('app', 'Contact Person Number'),
        ];
    }
}
