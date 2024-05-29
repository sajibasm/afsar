<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%item}}".
 *
 * @property integer $item_id
 * @property string $item_name
 * @property string $product_status
 *
 * @property Brand[] $brands
 * @property MarketBookSalesDetails[] $marketBookSalesDetails
 * @property ProductStatement[] $productStatements
 * @property ProductStockItems[] $productStockItems
 * @property ProductStockItemsDraft[] $productStockItemsDrafts
 * @property ProductStockStatement[] $productStockStatements
 * @property SalesDetails[] $salesDetails
 * @property SalesDraftItems[] $salesDraftItems
 * @property Size[] $sizes
 */
class Item extends \yii\db\ActiveRecord
{

    public $brand_id;

    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'product_status', 'brand_id'], 'required', 'on'=>['create', 'update']],
            [['product_status'], 'string'],
            [['brand_id'], 'integer'],
            [['item_name'], 'string', 'max' => 50],
            [['item_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => Yii::t('app', 'ID'),
            'item_name' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'product_status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrands()
    {
        return $this->hasMany(Brand::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketBookSalesDetails()
    {
        return $this->hasMany(MarketBookSalesDetails::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStatements()
    {
        return $this->hasMany(ProductStatement::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItems()
    {
        return $this->hasMany(ProductStockItems::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItemsDrafts()
    {
        return $this->hasMany(ProductStockItemsDraft::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockStatements()
    {
        return $this->hasMany(ProductStockStatement::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDraftItems()
    {
        return $this->hasMany(SalesDraftItems::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSizes()
    {
        return $this->hasMany(Size::className(), ['item_id' => 'item_id']);
    }
}
