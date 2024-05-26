<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%market_book_history}}".
 *
 * @property integer $market_sales_id
 * @property integer $sales_id
 * @property integer $client_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property string $unit
 * @property double $cost_amount
 * @property double $sales_amount
 * @property double $total_amount
 * @property integer $quantity
 * @property integer $user_id
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 */
class MarketBookHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%market_book_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_id', 'client_id', 'item_id', 'brand_id', 'size_id', 'user_id', 'remarks'], 'required'],
            [['sales_id', 'client_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'user_id'], 'integer'],
            [['cost_amount', 'sales_amount', 'total_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            [['unit'], 'string', 'max' => 20],
            [['remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'market_sales_id' => Yii::t('app', 'Market Sales ID'),
            'sales_id' => Yii::t('app', 'Sales ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'brand_id' => Yii::t('app', 'Brand ID'),
            'size_id' => Yii::t('app', 'Size ID'),
            'unit' => Yii::t('app', 'Unit'),
            'cost_amount' => Yii::t('app', 'Cost Amount'),
            'sales_amount' => Yii::t('app', 'Sales Amount'),
            'total_amount' => Yii::t('app', 'Total Amount'),
            'quantity' => Yii::t('app', 'Quantity'),
            'user_id' => Yii::t('app', 'User ID'),
            'remarks' => Yii::t('app', 'Remarks'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
