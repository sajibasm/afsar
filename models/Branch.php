<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%branch}}".
 *
 * @property integer $branch_id
 * @property integer $bank_id
 * @property string $branch_name
 *
 * @property Bank $bank
 * @property Lc[] $lcs
 */
class Branch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%branch}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bank_id', 'branch_name'], 'required'],
            [['bank_id'], 'integer'],
            [['branch_name'], 'string', 'max' => 100],
            ['branch_name', 'unique', 'targetAttribute' => ['bank_id', 'branch_name']]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'branch_id' => Yii::t('app', 'Branch ID'),
            'bank_id' => Yii::t('app', 'Bank ID'),
            'branch_name' => Yii::t('app', 'Branch Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcs()
    {
        return $this->hasMany(Lc::className(), ['branch_id' => 'branch_id']);
    }

    public function getBankList()
    {
        return Bank::find()->orderBy('bank_name')->all();
    }
}
