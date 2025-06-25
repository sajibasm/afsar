<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%brand_new}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $status
 * @property string $extra
 *
 * @property Item $item
 */
class BrandMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand_new}}';
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
            [['name', 'status'], 'required'],
            [['status', 'extra'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
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
            'status' => Yii::t('app', 'Status'),
            'extra' => Yii::t('app', 'Extra'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
    }
}
