<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sales_draft}}".
 *
 * @property integer $sales_details_id
 * @property integer $sales_id
 * @property integer $outletId
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $cost_amount
 * @property double $sales_amount
 * @property double $total_amount
 * @property double $quantity
 * @property string $challan_unit
 * @property double $challan_quantity
 * @property string $type
 * @property integer $user_id
 *
 * @property Item $item
 * @property User $user
 * @property Brand $brand
 * @property Size $size
 */
class SalesDraft extends \yii\db\ActiveRecord
{
    //'insert','update','return','update-added','update-deleted'
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_UPDATE_ADDED = 'update-added';
    const TYPE_UPDATE_DELETED = 'update-deleted';
    const TYPE_RETURN = 'return';
    const TYPE_SALES_PENDING = 'sales-pending';
    const TYPE_UPDATE_PENDING = 'update-pending';
    public $price;
    public $lowestPercent;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_draft}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'brand_id', 'size_id', 'sales_amount', 'quantity', 'price', 'outletId'], 'required', 'on'=>'create'],
            [['item_id', 'brand_id', 'size_id', 'sales_amount', 'quantity', 'outletId'], 'required', 'on'=>'update'],
            [['sales_id', 'item_id', 'brand_id', 'size_id', 'user_id', 'outletId'], 'integer'],
            [['item_id', 'brand_id', 'size_id'], 'required'],
            [['cost_amount', 'total_amount', 'quantity', 'challan_quantity', 'lowestPercent'], 'number'],
            [['quantity', 'price'], 'number', 'min'=>1],
            [['type'], 'string'],
            [['challan_unit'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sales_details_id' => Yii::t('app', 'Sales Details ID'),
            'sales_id' => Yii::t('app', 'Sales'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'cost_amount' => Yii::t('app', 'Cost Amount'),
            'sales_amount' => Yii::t('app', 'Rate'),
            'total_amount' => Yii::t('app', 'Total Amount'),
            'quantity' => Yii::t('app', 'Qty'),
            'challan_unit' => Yii::t('app', 'Challan Unit'),
            'challan_quantity' => Yii::t('app', 'Challan Qty'),
            'type' => Yii::t('app', 'Type'),

            'outletId' => Yii::t('app', 'Outlet'),

            'price' => Yii::t('app', 'Unit Price'),

            'user_id' => Yii::t('app', 'User'),
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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }


    public static function getUpdateDeleteTotal($salesId)
    {
        return SalesDraft::find()
            ->select('sum(total_amount) as total_amount')
            ->where(['user_id'=>Yii::$app->user->getId(), 'sales_id'=>$salesId, 'type'=>SalesDraft::TYPE_UPDATE_DELETED])
            ->one()->total_amount;
    }


    public static function getUpdateTotal($salesId)
    {
        return SalesDraft::find()
            ->select('sum(total_amount) as total_amount')
            ->andWhere(['user_id'=>Yii::$app->user->getId(), 'sales_id'=>$salesId])
            ->andWhere([
                'or',
                ['type'=>SalesDraft::TYPE_UPDATE],
                ['type'=>SalesDraft::TYPE_UPDATE_ADDED]
            ])->one()->total_amount;

    }




    public static function getTotal($salesId, $type, $userId)
    {
        if(!empty($salesId)){
            return SalesDraft::find()->select('sum(total_amount) as total_amount')->where(['user_id'=>$userId, 'type'=>$type, 'sales_id'=>$salesId])->one()->total_amount;
        }else{
            return SalesDraft::find()->select('sum(total_amount) as total_amount')->where(['user_id'=>$userId, 'type'=>$type])->one()->total_amount;
        }
    }

    public static function removeTemporary()
    {
        SalesDraft::deleteAll("user_id = '".Yii::$app->user->getId()."' AND type='".SalesDraft::TYPE_INSERT."' OR type='".SalesDraft::TYPE_RETURN."'");
    }

    public static function removeByUserId()
    {
        SalesDraft::deleteAll("user_id = '".Yii::$app->user->getId()."'");
    }


    public static function typeLabel($type)
    {
        if($type==self::TYPE_INSERT){
            return "Sales Hold";
        }elseif ($type==self::TYPE_UPDATE){
            return "Update";
        }elseif ($type==self::TYPE_UPDATE_ADDED){
            return "Update Hold";
        }elseif ($type==self::TYPE_UPDATE_DELETED){
            return "Update - Stock Back Processing";
        }elseif ($type==self::TYPE_RETURN){
            return "Return";
        }elseif ($type==self::TYPE_SALES_PENDING){
            return "Sales Pending";
        }
    }

}
