<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%transport}}".
 *
 * @property integer $transport_id
 * @property string $transport_name
 * @property string $transport_address
 * @property string $transport_contact_person
 * @property string $transport_contact_number
 *
 * @property Challan[] $challans
 */
class Transport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transport}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transport_name', 'transport_contact_number'], 'required'],
            [['transport_id'], 'integer'],
            [['transport_name', 'transport_contact_person'], 'string', 'max' => 50],
            [['transport_address'], 'string', 'max' => 500],
            [['transport_contact_number'], 'string', 'max' => 20],
            ['transport_name', 'unique', 'targetAttribute' => ['transport_name', 'transport_contact_number']]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'transport_id' => Yii::t('app', 'ID'),
            'transport_name' => Yii::t('app', 'Name'),
            'transport_address' => Yii::t('app', 'Address'),
            'transport_contact_person' => Yii::t('app', 'Contact Person'),
            'transport_contact_number' => Yii::t('app', 'Contact Number'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallans()
    {
        return $this->hasMany(Challan::className(), ['transport_id' => 'transport_id']);
    }
}
