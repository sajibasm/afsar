<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property integer $payment_type_id
 * @property string $payment_type_name
 * @property string $type
 * @property string $status
 *
 * @property ClientPaymentHistory[] $clientPaymentHistories
 */
class PaymentType extends \yii\db\ActiveRecord
{
    const  TYPE_CASH = 'Cash';
    const  TYPE_DEPOSIT = 'Bank';
    const  TYPE_SALES = 'Sales';

    const  TYPE_SALES_RETURN_ID = 3;
    const  TYPE_CASH_ID = 10;
    const  TYPE_BANK_ID = 11;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_type_name'], 'required'],
            [['payment_type_name'], 'unique'],
            [['payment_type_id'], 'integer'],
            [['payment_type_name', 'type'], 'string', 'max' => 100],
            [['status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_type_id' => Yii::t('app', 'ID'),
            'payment_type_name' => Yii::t('app', 'Payment Type'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPaymentHistories()
    {
        return $this->hasMany(ClientPaymentHistory::className(), ['payment_type_id' => 'payment_type_id']);
    }

    public function getTypeList()
    {
        return [
            self::TYPE_CASH=>self::TYPE_CASH.' Book',
            self::TYPE_DEPOSIT=>self::TYPE_DEPOSIT.' Book'
        ];
    }

}
