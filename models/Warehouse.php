<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%warehouse}}".
 *
 * @property integer $warehouse_id
 * @property string $warehouse_name
 * @property integer $city
 * @property string $address1
 * @property string $address2
 * @property string $postal_code
 *
 * @property ProductStock[] $productStocks
 * @property ProductStockDraft[] $productStockDrafts
 * @property City $city0
 * @property WarehousePayment[] $warehousePayments
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%warehouse}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'warehouse_name'], 'required'],
            [['city'], 'integer'],
            [['warehouse_name'], 'string', 'max' => 50],
            [['address1', 'address2'], 'string', 'max' => 200],
            [['postal_code'], 'string', 'max' => 10],
            ['warehouse_name', 'unique', 'targetAttribute' => ['city', 'warehouse_name']]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'warehouse_id' => Yii::t('app', 'Warehouse ID'),
            'warehouse_name' => Yii::t('app', 'Warehouse Name'),
            'city' => Yii::t('app', 'City'),
            'address1' => Yii::t('app', 'Address1'),
            'address2' => Yii::t('app', 'Address2'),
            'postal_code' => Yii::t('app', 'Postal Code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStocks()
    {
        return $this->hasMany(ProductStock::className(), ['warehouse_id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockDrafts()
    {
        return $this->hasMany(ProductStockDraft::className(), ['warehouse_id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasOne(City::className(), ['city_id' => 'city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehousePayments()
    {
        return $this->hasMany(WarehousePayment::className(), ['warehouse_id' => 'warehouse_id']);
    }
}
