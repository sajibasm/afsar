<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%client_financial_summary}}".
 *
 * @property int $id
 * @property int $client_id
 * @property float|null $total_cash_received
 * @property float|null $total_bank_received
 * @property float|null $total_cash_returned
 * @property float|null $total_bank_returned
 * @property float|null $total_reconciled
 * @property float|null $total_due
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Client $client
 */
class ClientFinancialSummary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%client_financial_summary}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id'], 'required'],
            [['client_id'], 'integer'],
            [
                [
                    'total_cash_received',
                    'total_bank_received',
                    'total_cash_returned',
                    'total_bank_returned',
                    'total_reconciled',
                    'total_due'
                ],
                'number'
            ],
            [['created_at', 'updated_at'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'client_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'total_cash_received' => Yii::t('app', 'Total Cash Received'),
            'total_bank_received' => Yii::t('app', 'Total Bank Received'),
            'total_cash_returned' => Yii::t('app', 'Total Cash Returned'),
            'total_bank_returned' => Yii::t('app', 'Total Bank Returned'),
            'total_reconciled' => Yii::t('app', 'Total Reconciled'),
            'total_due' => Yii::t('app', 'Total Due'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }
}
