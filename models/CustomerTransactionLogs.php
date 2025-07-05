<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer_transaction_logs}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $transaction_type
 * @property float $amount
 * @property string $action
 * @property int|null $reference_id
 * @property string|null $reference_model
 * @property string|null $remarks
 * @property int|null $performed_by
 * @property string|null $performed_at
 */
class CustomerTransactionLogs extends \yii\db\ActiveRecord
{
    const TRANSACTION_TYPES = [
        'purchase',
        'bank_received',
        'cash_received',
        'sales_return',
        'cash_return',
        'bank_return',
        'reconciliation',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer_transaction_logs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'transaction_type', 'amount', 'action'], 'required'],
            [['customer_id', 'reference_id', 'performed_by'], 'integer'],
            [['transaction_type'], 'in', 'range' => self::TRANSACTION_TYPES],
            [['transaction_type', 'action'], 'string'],
            [['amount'], 'number'],
            [['performed_at'], 'safe'],
            [['reference_model'], 'string', 'max' => 100],
            [['remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'transaction_type' => Yii::t('app', 'Transaction Type'),
            'amount' => Yii::t('app', 'Amount'),
            'action' => Yii::t('app', 'Action'),
            'reference_id' => Yii::t('app', 'Reference ID'),
            'reference_model' => Yii::t('app', 'Reference Model'),
            'remarks' => Yii::t('app', 'Remarks'),
            'performed_by' => Yii::t('app', 'Performed By'),
            'performed_at' => Yii::t('app', 'Performed At'),
        ];
    }
}
