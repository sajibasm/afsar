<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%product_statement}}".
 *
 * @property integer $product_statement_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property double $quantity
 * @property string $type
 * @property string $remarks
 * @property integer $reference_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Item $item
 * @property Brand $brand
 * @property Size $size
 */
class ProductStatement extends \yii\db\ActiveRecord
{
    const  TYPE_SALES = 'Sales';
    const  TYPE_SALES_UPDATE = 'Sales-Update';
    const  TYPE_SALES_RETURN = 'Sales-Return';
    const  TYPE_SALES_DELETE = 'Sales-Delete';


    const  TYPE_STOCK = 'Stock';
    const  TYPE_STOCK_RETURN = 'Stock-Return';

    const  TYPE_MARKET_SELL = 'Market-Sales';
    const  TYPE_MARKET_SELL_UPDATE = 'Market-Sales-Update';
    const  TYPE_MARKET_RETURN  = 'Market-Return';
    const  TYPE_STOCK_MOVEMENT  = 'Stock-Movement';
    const  TYPE_STOCK_MOVEMENT_REJECT  = 'Stock-Movement-Reject';
    const  TYPE_STOCK_TRANSFER  = 'Stock-Transfer';
    const  TYPE_STOCK_TRANSFER_REJECT  = 'Stock-Transfer-Reject';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_statement}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function() { return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'); }
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'brand_id', 'size_id',  'user_id'], 'required'],
            [['item_id', 'brand_id', 'size_id', 'reference_id', 'user_id'], 'integer'],
            [['quantity'], 'number'],
            [['type'], 'string'],
            [['remarks'], 'string', 'max'=>100],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_statement_id' => Yii::t('app', 'Product Statement ID'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'quantity' => Yii::t('app', 'Quantity'),
            'type' => Yii::t('app', 'Type'),
            'remarks' => Yii::t('app', 'Remarks'),
            'reference_id' => Yii::t('app', 'Ref ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
