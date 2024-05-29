<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\ProductOutletUtility;
use app\components\ProductUtility;
use app\components\Utility;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "product_stock_items_outlet".
 *
 * @property int $product_stock_items_outlet_id
 * @property int $product_stock_outlet_id
 * @property int $item_id
 * @property int $brand_id
 * @property int $size_id
 * @property int $transferOutlet
 * @property int $receivedOutlet
 * @property float|null $cost_price
 * @property float|null $wholesale_price
 * @property float|null $retail_price
 * @property float|null $previous_quantity
 * @property float|null $new_quantity
 * @property float|null $total_quantity
 * @property string $status
 */
class ProductStockItemsOutlet extends \yii\db\ActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';
    const STATUS_REJECTED = 'reject';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_stock_items_outlet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_stock_outlet_id', 'item_id', 'brand_id', 'size_id'], 'required'],
            [['product_stock_outlet_id', 'item_id', 'brand_id', 'size_id', 'transferOutlet', 'receivedOutlet'], 'integer'],
            [['cost_price', 'wholesale_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity'], 'number'],
            [['status'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_stock_items_outlet_id' => 'Product Stock Items Outlet ID',
            'product_stock_outlet_id' => 'Product Stock Outlet ID',
            'item_id' => 'Item ID',
            'brand_id' => 'Brand ID',
            'size_id' => 'Size ID',
            'transferOutlet' => 'Transfer Outlet',
            'receivedOutlet' => 'Received Outlet',
            'cost_price' => 'Cost Price',
            'wholesale_price' => 'Wholesale Price',
            'retail_price' => 'Retail Price',
            'previous_quantity' => 'Previous Quantity',
            'new_quantity' => 'New Quantity',
            'total_quantity' => 'Total Quantity',
            'status' => 'Status',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSize()
    {
        return $this->hasOne(Size::className(), ['size_id' => 'size_id']);
    }


    public static function draftToStockItems($productStockId, $items)
    {

        $data = [];
        foreach ($items as $item) {
            $previousQty = ProductUtility::getTotalQuantity($item->size_id);
            $data[] = [$productStockId, $item->item_id, $item->brand_id, $item->size_id, $item->cost_price,
                $item->retail_price, $previousQty, $item->new_quantity, $previousQty-$item->new_quantity, 'done'];
        }

        $totalRecord = Yii::$app->db->createCommand()->batchInsert('product_stock_items',
            ['product_stock_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'retail_price', 'previous_quantity', 'new_quantity', 'total_quantity', 'status'],
            $data
        )->execute();

        if (count($items) === $totalRecord) {
            return true;
        }

        return false;
    }

    public static function draftToStatementUpdate($productStockOutletId, $items, $remarks)
    {
        $data = [];

        foreach ($items as $item) {
            $data[] = [
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                (-1 * abs($item->new_quantity)),
                ProductStatement::TYPE_STOCK_TRANSFER,
                $productStockOutletId,
                Yii::$app->user->id,
                $remarks,
                DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka'),
                DateTimeUtility::getDate('', 'Y-m-d H:i:s', 'Asia/Dhaka')
            ];
        }

        $totalRecord = Yii::$app->db->createCommand()->batchInsert('product_statement',
            ['item_id', 'brand_id', 'size_id', 'quantity', 'type', 'reference_id', 'user_id', 'remarks', 'created_at', 'updated_at'],
            $data
        )->execute();

        if (count($items) === $totalRecord) {
            return true;
        }

        return false;

    }

    public static function draftToOutlet($productStockOutletId, $items, $transferOutlet, $receivedOutlet)
    {
        $data = [];

        foreach ($items as $item) {
            $previousQty = ProductOutletUtility::getTotalQuantity($item->size_id, $receivedOutlet);
            $data[] = [
                $productStockOutletId,
                $item->item_id,
                $item->brand_id,
                $item->size_id,
                0,
                0,
                $previousQty,
                $item->new_quantity,
                ($previousQty + $item->new_quantity),
                $transferOutlet,
                $receivedOutlet,
                self::STATUS_DONE,
            ];
        }


        $totalRecord = Yii::$app->db->createCommand()->batchInsert('product_stock_items_outlet',
            ['product_stock_outlet_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'retail_price',
                'previous_quantity', 'new_quantity', 'total_quantity', 'transferOutlet', 'receivedOutlet', 'status'],
            $data
        )->execute();

        if (count($items) === $totalRecord) {
            return true;
        }

        return false;

    }
}
