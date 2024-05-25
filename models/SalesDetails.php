<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sales_details}}".
 *
 * @property integer $sales_details_id
 * @property integer $outletId
 * @property integer $sales_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property string $unit
 * @property double $cost_amount
 * @property double $sales_amount
 * @property double $total_amount
 * @property double $quantity
 * @property string $challan_unit
 * @property double $challan_quantity
 * @property string $status
 *
 * @property Brand $brand
 * @property Sales $sales
 * @property Item $item
 * @property Size $size
 *
 * @property SalesReturnDetails $salesReturnDetails
 */
class SalesDetails extends \yii\db\ActiveRecord
{

    public $item_name;
    public $brand_name;
    public $size_name;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DELETE = 'delete';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_details}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_id', 'item_id', 'brand_id', 'size_id', 'outletId'], 'required'],
            [['sales_id', 'item_id', 'brand_id', 'size_id', 'outletId'], 'integer'],
            [['cost_amount', 'sales_amount', 'total_amount', 'quantity', 'challan_quantity'], 'number'],
            [['unit', 'challan_unit'], 'string', 'max' => 20],
            [['status'], 'string'],
            [['item_name', 'brand_name', 'size_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sales_details_id' => Yii::t('app', 'Sales Details ID'),
            'sales_id' => Yii::t('app', 'Invoice'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'unit' => Yii::t('app', 'Unit'),
            'cost_amount' => Yii::t('app', 'Cost'),
            'sales_amount' => Yii::t('app', 'Sales'),
            'total_amount' => Yii::t('app', 'Total'),
            'quantity' => Yii::t('app', 'Qty'),
            'challan_unit' => Yii::t('app', 'Ch Unit'),
            'challan_quantity' => Yii::t('app', 'Challan Qty'),
            'outletId' => Yii::t('app', 'Outlet'),
            'status' => Yii::t('app', 'Status'),
        ];
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
    public function getSales()
    {
        return $this->hasOne(Sales::className(), ['sales_id' => 'sales_id']);
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
    public function getSize()
    {
        return $this->hasOne(Size::className(), ['size_id' => 'size_id']);
    }

    public function getSalesReturnDetails()
    {
        return $this->hasOne(SalesReturnDetails::className(), ['sales_id'=>'sales_id', 'size_id'=>'size_id']);
    }
}
