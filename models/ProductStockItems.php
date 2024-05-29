<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%product_stock_items}}".
 *
 * @property integer $product_stock_items_id
 * @property integer $product_stock_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $cost_price
 * @property double $wholesale_price
 * @property double $retail_price
 * @property double $previous_quantity
 * @property double $new_quantity
 * @property double $total_quantity
 * @property string $status
 *
 * @property Item $item
 * @property Brand $brand
 * @property Size $size
 * @property ProductStock $productStock
 */
class ProductStockItems extends \yii\db\ActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';

    public $created_at;
    public $created_to;
    public $warehouse;
    public $lc;
    public $type;
    public $supplier;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_stock_items}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_stock_id', 'item_id', 'brand_id', 'size_id'], 'required'],
            [['product_stock_id', 'item_id', 'brand_id', 'size_id'], 'integer'],
            [['created_at', 'created_to', 'type', 'status'], 'safe'],
            [['cost_price', 'wholesale_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_stock_items_id' => Yii::t('app', 'Product Stock Items ID'),
            'product_stock_id' => Yii::t('app', 'Product Stock ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'brand_id' => Yii::t('app', 'Brand ID'),
            'size_id' => Yii::t('app', 'Size ID'),
            'cost_price' => Yii::t('app', 'Cost Price'),
            'wholesale_price' => Yii::t('app', 'Wholesale Price'),
            'retail_price' => Yii::t('app', 'Retail Price'),
            'previous_quantity' => Yii::t('app', 'Previous Qty'),
            'new_quantity' => Yii::t('app', 'New Qty'),
            'total_quantity' => Yii::t('app', 'Total Qty'),
            'status' => Yii::t('app', 'Status'),

            'created_at' => Yii::t('app', 'Date'),
            'created_to' => Yii::t('app', 'To'),

            'warehouse' => Yii::t('app', 'Warehouse'),
            'lc' => Yii::t('app', 'LC'),
            'type' => Yii::t('app', 'Type'),
            'supplier' => Yii::t('app', 'Supplier'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSize()
    {
        return $this->hasOne(Size::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStock()
    {
        return $this->hasOne(ProductStock::className(), ['product_stock_id' => 'product_stock_id']);
    }
}
