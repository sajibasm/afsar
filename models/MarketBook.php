<?php

namespace app\models;

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%market_book}}".
 *
 * @property integer $market_sales_id
 * @property integer $sales_id
 * @property integer $client_id
 * @property integer $item_id
 * @property integer $brand_id
 * @property integer $size_id
 * @property string $unit
 * @property double $cost_amount
 * @property double $sales_amount
 * @property double $total_amount
 * @property integer $quantity
 * @property integer $user_id
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 *
 * @property Item $item
 * @property Brand $brand
 * @property Size $size
 * @property Client $client
 */
class MarketBook extends \yii\db\ActiveRecord
{
    const  STATUS_SELL = 'Sold';
    const  STATUS_RETURN = 'Return';
    const  STATUS_DONE = 'Done';

    public $price;
    public $returnQuantity;

    public $clients;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%market_book}}';
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
            [['client_id'], 'required', 'on'=>'customer'],
            [['client_id', 'item_id', 'brand_id', 'size_id', 'sales_amount', 'total_amount', 'cost_amount'], 'required', 'on'=>'sell'],
            [['sales_id','client_id', 'item_id', 'brand_id', 'size_id', 'quantity', 'user_id', 'returnQuantity'], 'integer'],
            [['cost_amount', 'sales_amount', 'total_amount', 'price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['status', 'clients'], 'string'],
            [['unit'], 'string', 'max' => 20],
            [['quantity'], 'integer', 'min' => 1],
            [['remarks'], 'string', 'max' => 200],
            ['returnQuantity', function ($attribute, $params) {
                $availableQty = CustomerUtility::marketReturnableQty($this->client_id, $this->size_id);
                if ($this->returnQuantity > $availableQty) {
                    $this->addError($attribute, 'Return Qty '.$this->returnQuantity.' should be less or equal total Sold Qty: '.$availableQty);
                    return false;
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'market_sales_id' => Yii::t('app', 'Market Sales ID'),
            'sales_id' => Yii::t('app', 'Customer'),
            'client_id' => Yii::t('app', 'Customer'),
            'item_id' => Yii::t('app', 'Item'),
            'brand_id' => Yii::t('app', 'Brand'),
            'size_id' => Yii::t('app', 'Size'),
            'unit' => Yii::t('app', 'Unit'),
            'cost_amount' => Yii::t('app', 'Cost'),
            'sales_amount' => Yii::t('app', 'Price'),
            'total_amount' => Yii::t('app', 'Total'),
            'quantity' => Yii::t('app', 'Quantity'),
            'remarks' => Yii::t('app', 'Remarks'),
            'user_id' => Yii::t('app', 'User'),
            'returnQuantity' => Yii::t('app', 'Return Qty'),

            'status' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Unit Price'),

            'created_to' => Yii::t('app', 'To'),
            'created_at' => Yii::t('app', 'Date'),
            'updated_at' => Yii::t('app', 'Return'),
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
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }
}
