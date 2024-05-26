<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sales_return_details}}".
 *
 * @property integer $sales_details_id
 * @property integer $sales_return_id
 * @property integer $sales_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $refund_amount
 * @property double $sales_amount
 * @property double $total_amount
 * @property double $quantity
 *
 * @property Item $item
 * @property Brand $brand
 * @property Size $size
 * @property SalesReturn $salesReturn
 */
class SalesReturnDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_return_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_return_id', 'item_id', 'brand_id', 'size_id'], 'required'],
            [['sales_return_id', 'item_id', 'brand_id', 'size_id', 'sales_id'], 'integer'],
            [['refund_amount', 'sales_amount', 'total_amount', 'quantity'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sales_details_id' => Yii::t('app', 'Sales Details ID'),
            'sales_return_id' => Yii::t('app', 'Sales Return ID'),
            'sales_id' => Yii::t('app', 'Sales ID'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'refund_amount' => Yii::t('app', 'Cost Amount'),
            'sales_amount' => Yii::t('app', 'Sales Amount'),
            'total_amount' => Yii::t('app', 'Total Amount'),
            'quantity' => Yii::t('app', 'Quantity'),
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
    public function getSalesReturn()
    {
        return $this->hasOne(SalesReturn::className(), ['sales_return_id' => 'sales_return_id']);
    }

    public static function getReceivedItemDetailsBySalesAndSize($salesId, $sizeId)
    {
        return SalesReturnDetails::find()->where(['sales_id'=>$salesId, 'size_id'=>$sizeId])->all();
    }

}
