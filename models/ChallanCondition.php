<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%challan_condition}}".
 *
 * @property integer $challan_condition_id
 * @property string $challan_condition_name
 *
 * @property Challan[] $challans
 */
class ChallanCondition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%challan_condition}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['challan_condition_name'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'challan_condition_id' => Yii::t('app', 'Challan Condition ID'),
            'challan_condition_name' => Yii::t('app', 'Challan Condition Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChallans()
    {
        return $this->hasMany(Challan::className(), ['condition_id' => 'challan_condition_id']);
    }
}
