<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%client_transaction_summary}}".
 *
 * @property int $id
 * @property int $client_id
 * @property string $transaction_type
 * @property string $transaction_mode
 * @property string|null $reference_table
 * @property int|null $reference_id
 * @property float $transaction_amount
 * @property string|null $remarks
 * @property int $created_by
 * @property string|null $created_at
 *
 * @property Client $client
 * @property User $user
 */
class ClientTransactionSummary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%client_transaction_summary}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'transaction_type', 'transaction_mode', 'transaction_amount', 'created_by'], 'required'],
            [['client_id', 'reference_id', 'created_by'], 'integer'],
            [['transaction_type', 'transaction_mode'], 'string'],
            [['transaction_amount'], 'number'],
            [['created_at'], 'safe'],
            [['reference_table'], 'string', 'max' => 100],
            [['remarks'], 'string', 'max' => 255],
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
            'transaction_type' => Yii::t('app', 'Type'),
            'transaction_mode' => Yii::t('app', 'Mode'),
            'reference_table' => Yii::t('app', 'Reference'),
            'reference_id' => Yii::t('app', 'Ref ID'),
            'transaction_amount' => Yii::t('app', 'Amount'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'created_at',
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

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'created_by']);
    }
}
