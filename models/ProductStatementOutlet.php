<?php

namespace app\models;

use app\components\DateTimeUtility;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product_statement_outlet".
 *
 * @property int $product_statement_outlet_id
 * @property int $outlet_id
 * @property int $item_id
 * @property int $brand_id
 * @property int $size_id
 * @property float|null $quantity
 * @property string|null $type
 * @property string $remarks
 * @property int|null $reference_id
 * @property int $user_id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ProductStatementOutlet extends \yii\db\ActiveRecord
{

    const TYPE_RECEIVED='Stock-Received';
    const TYPE_TRANSFER='Stock-Outlet-Transfer';


    const  TYPE_SALES = 'Sales';
    const  TYPE_SALES_UPDATE = 'Sales-Update';
    const  TYPE_SALES_RETURN = 'Sales-Return';
    const  TYPE_SALES_DELETE = 'Sales-Delete';

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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_statement_outlet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['outlet_id', 'item_id', 'brand_id', 'size_id', 'remarks', 'user_id'], 'required'],
            [['outlet_id', 'item_id', 'brand_id', 'size_id', 'reference_id', 'user_id'], 'integer'],
            [['quantity'], 'number'],
            [['type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_statement_outlet_id' => 'Product Statement Outlet ID',
            'outlet_id' => 'Outlet ID',
            'item_id' => 'Item ID',
            'brand_id' => 'Brand ID',
            'size_id' => 'Size ID',
            'quantity' => 'Quantity',
            'type' => 'Type',
            'remarks' => 'Remarks',
            'reference_id' => 'Reference ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'outlet_id']);
    }

    public function getItemDetail()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
    }

    public function getBrandDetail()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    public function getSizeDetail()
    {
        return $this->hasOne(Size::className(), ['size_id' => 'size_id']);
    }


    public function getUserDetail()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}
