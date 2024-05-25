<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%return_draft}}".
 *
 * @property integer $return_draft_id
 * @property integer $sales_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $refund_amount
 * @property double $sales_amount
 * @property double $total_amount
 * @property double $quantity
 * @property integer $user_id
 *
 * @property Brand $brand
 * @property Item $item
 * @property Size $size
 */
class ReturnDraft extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%return_draft}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_id', 'item_id', 'brand_id', 'size_id', 'user_id'], 'integer'],
            [['item_id', 'brand_id', 'size_id'], 'required'],
            [['refund_amount', 'total_amount', 'quantity', 'sales_amount'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'return_draft_id' => Yii::t('app', 'Return Draft ID'),
            'sales_id' => Yii::t('app', 'Sales'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'refund_amount' => Yii::t('app', 'Refund Amount'),
            'sales_amount' => Yii::t('app', 'Sales Amount'),
            'total_amount' => Yii::t('app', 'Total Amount'),
            'quantity' => Yii::t('app', 'Quantity'),
            'user_id' => Yii::t('app', 'User'),
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

    public static function getAdjustmentAmountBySalesId(&$salesId)
    {
        $total = 0;
        $models =  ReturnDraft::find()->where(['user_id'=>Yii::$app->user->getId(),'sales_id'=>$salesId])->all();
        foreach ($models as $model){
            $total+=($model->sales_amount*$model->quantity) - ($model->refund_amount*$model->quantity);
        }
        return $total;
    }

    public static function getTotal(&$salesId)
    {
        return ReturnDraft::find()->select('sum(total_amount) as total_amount')->where(['user_id'=>Yii::$app->user->getId(),'sales_id'=>$salesId])->one()->total_amount;
    }
}
