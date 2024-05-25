<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%product_stock_items_draft}}".
 *
 * @property integer $product_stock_items_draft_id
 * @property integer $product_stock_items_id
 * @property integer $user_id
 * @property integer $outletId
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $cost_price
 * @property double $wholesale_price
 * @property double $retail_price
 * @property double $new_quantity
 * @property integer $alert_quantity
 * @property string $type
 * @property string $source
 *
 * @property Item $item
 * @property ProductStockDraft $productStockDraft
 * @property Brand $brand
 * @property Size $size
 */
class ProductStockItemsDraft extends \yii\db\ActiveRecord
{

    const TYPE_INSERT = 'Insert';
    const TYPE_UPDATE = 'Update';
    const SOURCE_STOCK = 'Stock';
    const SOURCE_MOVEMENT = 'Movement';
    const SOURCE_TRANSFER = 'Transfer';

    public $totalQuantity = 0;

    public $outlet;


    public $itemName;
    public $brandName;
    public $sizeName;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_stock_items_draft}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['item_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price', 'new_quantity'], 'required', 'on'=>'stockDraft'],

            [['size_id', 'user_id'], 'unique', 'targetAttribute'=>'size_id', 'message' => 'This product already exist in your Cart.'],

            [['product_stock_items_draft_id', 'product_stock_items_id','item_id', 'brand_id', 'size_id', 'alert_quantity', 'user_id'], 'integer'],
            
            [['cost_price', 'new_quantity', 'alert_quantity'], 'number', 'min'=>1],
            [['outletId'],'integer'],

            [['type'], 'string', 'max' => 10],
            [['source'], 'string', 'max' => 10],

            [['itemName', 'brandName', 'sizeName'], 'string'],
            
            [['alert_quantity', 'new_quantity', 'retail_price', 'wholesale_price', 'cost_price'], 'trim'],

            ['wholesale_price', 'checkWholeSalePrice'],
            ['retail_price', 'checkRetailPrice'],

        ];
    }

    public function checkWholeSalePrice($attribute, $params)
    {
        if($this->$attribute<$this->cost_price){
            $this->addError($attribute, Yii::t('app', 'Wholesale price can\'t be less than cost price.'));
        }

    }


    public function checkRetailPrice($attribute, $params)
    {
        // no real check at the moment to be sure that the error is triggered
        if($this->$attribute<$this->wholesale_price){
            $this->addError($attribute, Yii::t('app', 'Retail price can\'t be less than wholesale price.'));
        }
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_stock_items_draft_id' => Yii::t('app', 'Product Stock Items Draft ID'),
            'product_stock_items_id' => Yii::t('app', 'Product Item Id'),
            'user_id' => Yii::t('app', 'User'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'cost_price' => Yii::t('app', 'Cost Price'),
            'wholesale_price' => Yii::t('app', 'Wholesale Price'),
            'retail_price' => Yii::t('app', 'Retail Price'),
            'new_quantity' => Yii::t('app', 'New Qty'),
            'alert_quantity' => Yii::t('app', 'Alert Qty (%)'),

            'itemName' => Yii::t('app', 'Item'),
            'brandName' => Yii::t('app', 'Brand'),
            'sizeName' => Yii::t('app', 'Size'),
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

    public function getTotalQuantity()
    {
        $model = ProductStockItemsDraft::find()->select(['COUNT(*) AS totalQuantity'])->where(['user_id'=>Yii::$app->user->getId()])->one();
        $this->totalQuantity =  $model->totalQuantity;
    }




}
