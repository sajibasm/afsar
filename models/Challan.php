<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%challan}}".
 *
 * @property integer $challan_id
 * @property integer $sales_id
 * @property integer $client_id
 * @property double $amount
 * @property integer $transport_id
 * @property string $transport_invoice_number
 * @property integer $condition_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Sales $sales
 * @property Client $client
 * @property Transport $transport
 * @property ChallanCondition $condition
 */
class Challan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%challan}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function() { return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'); }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['challan_id', 'sales_id', 'client_id', 'condition_id'], 'required'],
            [['challan_id', 'sales_id', 'client_id', 'transport_id', 'condition_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['transport_invoice_number'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'challan_id' => Yii::t('app', 'Challan ID'),
            'sales_id' => Yii::t('app', 'Sales ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'amount' => Yii::t('app', 'Amount'),
            'transport_id' => Yii::t('app', 'Transport ID'),
            'transport_invoice_number' => Yii::t('app', 'Transport Invoice Number'),
            'condition_id' => Yii::t('app', 'Condition ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasOne(Sales::className(), ['sales_id' => 'sales_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['transport_id' => 'transport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondition()
    {
        return $this->hasOne(ChallanCondition::className(), ['challan_condition_id' => 'condition_id']);
    }
}
