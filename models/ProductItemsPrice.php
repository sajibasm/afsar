<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%product_items_price}}".
 *
 * @property integer $product_stock_items_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $cost_price
 * @property double $wholesale_price
 * @property double $retail_price
 * @property double $quantity
 * @property double $alert_quantity
 *
 * @property Item $item
 * @property Brand $brand
 * @property Size $size
 */
class ProductItemsPrice extends \yii\db\ActiveRecord
{
    public $item_name;
    public $brand_name;
    public $size_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_items_price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'brand_id', 'size_id', 'alert_quantity'], 'required'],
            [['item_id', 'brand_id', 'size_id'], 'integer'],
            [['cost_price', 'wholesale_price', 'retail_price', 'quantity' ,'alert_quantity'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_stock_items_id' => Yii::t('app', 'Product Stock Items ID'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),

            'item_name' => Yii::t('app', 'Item'),
            'brand_name' => Yii::t('app', 'Brand'),
            'size_name' => Yii::t('app', 'Size'),

            'cost_price' => Yii::t('app', 'Cost Price'),
            'wholesale_price' => Yii::t('app', 'Wholesale Price'),
            'retail_price' => Yii::t('app', 'Retail Price'),
            'quantity' => Yii::t('app', 'Quantity'),
            'alert_quantity' => Yii::t('app', 'Alert Quantity'),
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
}
