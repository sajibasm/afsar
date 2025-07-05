<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer_financial_summary}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property float|null $total_dues
 * @property float|null $total_discount
 * @property string|null $last_updated
 */
class CustomerFinancialSummary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer_financial_summary}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id'], 'required'],
            [['customer_id'], 'integer'],
            [['total_dues', 'total_discount'], 'number'],
            [['last_updated'], 'safe'],
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
            'total_dues' => Yii::t('app', 'Total Dues'),
            'total_discount' => Yii::t('app', 'Total Discount'),
            'last_updated' => Yii::t('app', 'Last Updated'),
        ];
    }
}
