<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%lc_payment_type}}".
 *
 * @property integer $lc_payment_type_id
 * @property string $lc_payment_type_name
 * @property integer $lc_payment_type_status
 *
 * @property LcPayment[] $lcPayments
 */
class LcPaymentType extends \yii\db\ActiveRecord
{

    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lc_payment_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lc_payment_type_name', 'lc_payment_type_status'], 'required'],
            [['lc_payment_type_id'], 'integer'],
            [['lc_payment_type_name'], 'string', 'max' => 50],
            [['lc_payment_type_status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lc_payment_type_id' => Yii::t('app', 'ID'),
            'lc_payment_type_name' => Yii::t('app', 'Type Name'),
            'lc_payment_type_status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcPayments()
    {
        return $this->hasMany(LcPayment::className(), ['lc_payment_type' => 'lc_payment_type_id']);
    }

    public static function getStatusList()
    {
        return [self::ACTIVE=>'Active', self::INACTIVE=>'Inactive'];
    }
}
