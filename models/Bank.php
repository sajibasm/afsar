<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%bank}}".
 *
 * @property integer $bank_id
 * @property string $bank_name
 *
 * @property Branch[] $branches
 */
class Bank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%bank}}';
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bank_name'], 'string', 'max' => 100],
            [['bank_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bank_id' => Yii::t('app', 'Bank ID'),
            'bank_name' => Yii::t('app', 'Bank Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(Branch::className(), ['bank_id' => 'bank_id']);
    }
}
