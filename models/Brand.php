<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%brand}}".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property integer $item_id
 * @property string $brand_name
 * @property string $brand_status
 *
 * @property Item $item
 * @property MarketBookSalesDetails[] $marketBookSalesDetails
 * @property ProductStatement[] $productStatements
 * @property ProductStockItems[] $productStockItems
 * @property ProductStockItemsDraft[] $productStockItemsDrafts
 * @property ProductStockStatement[] $productStockStatements
 * @property SalesDetails[] $salesDetails
 * @property SalesDraftItems[] $salesDraftItems
 * @property Size[] $sizes
 */
class Brand extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'required'],
            [['item_id'], 'required'],
            [['item_id'], 'integer'],
            [['brand_status'], 'string'],
            [['brand_name'], 'string', 'max' => 50],
            ['brand_name', 'unique', 'targetAttribute' => ['item_id', 'brand_name']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'brand_id' => Yii::t('app', 'Brand'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_name' => Yii::t('app', 'Brand'),
            'brand_status' => Yii::t('app', 'Status'),
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
    public function getMarketBookSalesDetails()
    {
        return $this->hasMany(MarketBookSalesDetails::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStatements()
    {
        return $this->hasMany(ProductStatement::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItems()
    {
        return $this->hasMany(ProductStockItems::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItemsDrafts()
    {
        return $this->hasMany(ProductStockItemsDraft::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockStatements()
    {
        return $this->hasMany(ProductStockStatement::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDraftItems()
    {
        return $this->hasMany(SalesDraftItems::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSizes()
    {
        return $this->hasMany(Size::className(), ['brand_id' => 'brand_id']);
    }

}
