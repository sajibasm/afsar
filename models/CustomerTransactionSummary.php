<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer_transaction_summary}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $transaction_type
 * @property float|null $total_amount
 * @property int|null $reference_id
 * @property string|null $reference_model
 * @property string|null $last_transaction_date
 * @property int|null $last_updated_by
 * @property string|null $last_updated
 * @property string|null $payment_status
 */
class CustomerTransactionSummary extends \yii\db\ActiveRecord
{
    const TYPE_PURCHASE       = 'purchase';
    const TYPE_BANK_RECEIVED  = 'bank_received';
    const TYPE_CASH_RECEIVED  = 'cash_received';
    const TYPE_SALES_RETURN   = 'sales_return';
    const TYPE_CASH_RETURN    = 'cash_return';
    const TYPE_BANK_RETURN    = 'bank_return';
    const TYPE_RECONCILIATION = 'reconciliation';

    const TRANSACTION_TYPES = [
        self::TYPE_PURCHASE,
        self::TYPE_BANK_RECEIVED,
        self::TYPE_CASH_RECEIVED,
        self::TYPE_SALES_RETURN,
        self::TYPE_CASH_RETURN,
        self::TYPE_BANK_RETURN,
        self::TYPE_RECONCILIATION,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer_transaction_summary}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'transaction_type'], 'required'],
            [['customer_id', 'reference_id', 'last_updated_by'], 'integer'],
            [['transaction_type'], 'in', 'range' => self::TRANSACTION_TYPES],
            [['transaction_type', 'payment_status'], 'string'],
            [['total_amount'], 'number'],
            [['last_transaction_date', 'last_updated'], 'safe'],
            [['reference_model'], 'string', 'max' => 100],
            [['customer_id', 'transaction_type'], 'unique', 'targetAttribute' => ['customer_id', 'transaction_type']],
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
            'total_amount' => Yii::t('app', 'Total Amount'),
            'reference_id' => Yii::t('app', 'Reference ID'),
            'reference_model' => Yii::t('app', 'Reference Model'),
            'last_transaction_date' => Yii::t('app', 'Last Transaction Date'),
            'last_updated_by' => Yii::t('app', 'Last Updated By'),
            'last_updated' => Yii::t('app', 'Last Updated'),
            'payment_status' => Yii::t('app', 'Payment Status'),
        ];
    }
}
