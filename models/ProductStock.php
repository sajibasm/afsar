<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\ProductUtility;
use app\components\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%product_stock}}".
 *
 * @property integer $product_stock_id
 * @property integer $warehouse_id
 * @property integer $lc_id
 * @property integer $user_id
 * @property string $type
 * @property string $remarks
 * @property string $params
 * @property integer $buyer_id
 * @property string $invoice_no
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 *
 * @property Warehouse $warehouse
 * @property Lc $lc
 * @property User $user
 * @property Buyer $supplier
 * @property ProductStockItems[] $productStockItems
 */
class ProductStock extends ActiveRecord
{
    const TYPE_IMPORT = 'Import';
    const TYPE_LOCAL = 'Local';
    const TYPE_MOVEMENT = 'Movement';
    const TYPE_TRANSFER = 'Transfer';
    const TYPE_RECEIVED = 'Received';
    const TYPE_MIGRATION = 'Migration';


    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECT = 'reject';

    public $outlet;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_stock}}';
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
                'value' => function () {
                    return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required', 'on' => 'stock'],
            [['type'], 'required', 'on' => 'migration'],
            [['type', 'outlet'], 'required', 'on' => 'transfer'],
            [['warehouse_id', 'lc_id', 'user_id', 'buyer_id',], 'integer'],
            [['user_id'], 'required'],
            [['type'], 'string'],
            [['invoice_no'], 'string', 'max' => 20],
            [['remarks'], 'string', 'max' => 300],
            [['params'], 'string'],
            [['outlet'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['invoice_no', 'remarks'], 'trim'],
            [['status'], 'string'],

        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                if ($this->type == ProductStock::TYPE_LOCAL) {
                    $this->lc_id = null;
                    $this->warehouse_id = null;
                } else {
                    $this->buyer_id = null;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_stock_id' => Yii::t('app', 'Product Stock ID'),
            'warehouse_id' => Yii::t('app', 'Warehouse'),
            'lc_id' => Yii::t('app', 'LC'),
            'user_id' => Yii::t('app', 'User'),
            'type' => Yii::t('app', 'Type'),
            'remarks' => Yii::t('app', 'Note'),
            'params' => Yii::t('app', 'Params'),
            'buyer_id' => Yii::t('app', 'Supplier'),
            'invoice_no' => Yii::t('app', 'Invoice'),
            'outlet' => Yii::t('app', 'Received'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function afterFind()
    {
        if (empty($this->remarks)) {
            $this->remarks = "N/A";
        }

        if (empty($this->invoice_no)) {
            $this->invoice_no = "N/A";
        }

        $this->created_at = Yii::$app->formatter->asDate($this->created_at);
        $this->updated_at = Yii::$app->formatter->asDate($this->updated_at);
        return parent::afterFind();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['warehouse_id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLc()
    {
        return $this->hasOne(Lc::className(), ['lc_id' => 'lc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Buyer::className(), ['id' => 'buyer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItems()
    {
        return $this->hasMany(ProductStockItems::className(), ['product_stock_id' => 'product_stock_id']);
    }

    public static function stockDraftRemove($type = ProductStockItemsDraft::TYPE_INSERT, $source = ProductStockItemsDraft::SOURCE_STOCK)
    {
        return ProductStockItemsDraft::deleteAll(['user_id' => Yii::$app->user->getId(), 'type' => $type, 'source' => $source]);
    }

    public static function stockSave(ProductStock $model)
    {
        $productStockItemsModel = new ProductStockItems();
        $productStockItemsDraft = ProductStockItemsDraft::find()->where(['user_id' => Yii::$app->user->getId(), 'type' => 'insert', 'source' => ProductStockItemsDraft::SOURCE_STOCK])->all();


        $items = [];
        $statement = [];
        $price = [];

        $isSave = true;

        foreach ($productStockItemsDraft as $draftItem) {

            $items[] = [
                $model->product_stock_id,
                $draftItem->item_id,
                $draftItem->brand_id,
                $draftItem->size_id,
                $draftItem->cost_price,
                $draftItem->wholesale_price,
                $draftItem->retail_price,
                ProductUtility::getTotalQuantity($draftItem->size_id),
                $draftItem->new_quantity,
                $draftItem->new_quantity + ProductUtility::getTotalQuantity($draftItem->size_id),
                ProductStockItems::STATUS_DONE
            ];

            $statement[] = [
                $draftItem->item_id,
                $draftItem->brand_id,
                $draftItem->size_id,
                $draftItem->new_quantity,
                ProductStatement::TYPE_STOCK,
                'success',
                $model->product_stock_id,
                Yii::$app->user->getId()
            ];

            $productItemsPrice = ProductItemsPrice::find()->where(['size_id' => $draftItem->size_id])->one();
            if ($productItemsPrice) {
                $productItemsPrice->cost_price = $draftItem->cost_price;
                $productItemsPrice->wholesale_price = $draftItem->wholesale_price;
                $productItemsPrice->quantity = $draftItem->new_quantity;
                $productItemsPrice->retail_price = $draftItem->retail_price;
                $productItemsPrice->alert_quantity = $draftItem->alert_quantity;
                if (!$productItemsPrice->save()) {
                    $isSave = false;
                }
            } else {
                $price[] = [
                    $draftItem->item_id,
                    $draftItem->brand_id,
                    $draftItem->size_id,
                    $draftItem->cost_price,
                    $draftItem->wholesale_price,
                    $draftItem->retail_price,
                    $draftItem->new_quantity,
                    $draftItem->new_quantity,
                    $draftItem->alert_quantity
                ];
            }
        }

        if (count($items) > 0 && $isSave) {

            $insert = Yii::$app->db->createCommand()->batchInsert(ProductStockItems::tableName(), [
                'product_stock_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price',
                'previous_quantity', 'new_quantity', 'total_quantity', 'status'
            ], $items)->execute();

            if (count($items) === $insert && count($statement) > 0) {

                $insert = Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), [
                    'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks', 'reference_id', 'user_id'
                ], $statement)->execute();

                if (count($statement) === $insert) {
                    if (count($price) > 0) {
                        $insert = Yii::$app->db->createCommand()->batchInsert(ProductItemsPrice::tableName(), [
                            'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price', 'quantity', 'total_quantity', 'alert_quantity'
                        ], $price)->execute();

                        if (count($price) === $insert) {
                            if(self::stockDraftRemove()){
                                return true;
                            }
                        }
                    }
                    if(self::stockDraftRemove()){
                        return true;
                    }
                }
            }
        }

        return false;
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

    public static function saveOutletStock(ProductStock $productStock, $requestedData, $isDeleteItems, $outlet)
    {

        $productStockOutlet = new ProductStockOutlet();
        $productStockOutlet->product_stock_outlet_code = uniqid(rand(1, 9999));
        $productStockOutlet->invoice = $productStock->invoice_no = Utility::genInvoice('STR-');
        $productStockOutlet->ref = $productStock->product_stock_id;
        $productStockOutlet->note = $productStock->remarks;
        $productStockOutlet->type = ProductStockOutlet::TYPE_RECEIVED;
        $productStockOutlet->remarks =$productStock->remarks;
        $productStockOutlet->params = Json::encode(['receivedOutlet'=>$outlet->name, 'coreStock'=>$productStock->product_stock_id, 'mode'=>'single']);
        $productStockOutlet->transferFrom = ProductStockOutlet::TRANSFER_FROM_STOCK;
        $productStockOutlet->transferOutlet = -1;
        $productStockOutlet->receivedFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
        $productStockOutlet->receivedOutlet = $productStock->outlet;
        $productStockOutlet->transferBy = $productStock->user_id;
        $productStockOutlet->status = ProductStockOutlet::STATUS_PENDING;
        if ($productStockOutlet->save()){
            $isDraftToOutletSave = ProductStockItemsOutlet::draftToOutlet($productStockOutlet->product_stock_outlet_id, $isDeleteItems, $productStockOutlet->transferOutlet, $productStockOutlet->receivedOutlet);
            $isDraftToStatementUpdate = ProductStockItemsOutlet::draftToStatementUpdate($productStock->product_stock_id, $isDeleteItems, $productStockOutlet->invoice);
            if($isDraftToOutletSave && $isDraftToStatementUpdate){
                $params = Json::decode($productStockOutlet->params);
                $params['outletStock'] = $productStockOutlet->product_stock_outlet_id;
                $productStock->params = Json::encode($params);
                if($productStock->save()){
                    $isDeleteItems = ProductStockItemsDraft::deleteAll(['source' => ProductStockItemsDraft::SOURCE_TRANSFER, 'user_id' => Yii::$app->user->id]);
                    if($isDeleteItems){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function getTypeList(){
        return [
            ProductStock::TYPE_LOCAL=>ProductStock::TYPE_LOCAL,
            ProductStock::TYPE_IMPORT=>ProductStock::TYPE_IMPORT,
            ProductStock::TYPE_MOVEMENT=>ProductStock::TYPE_MOVEMENT,
            ProductStock::TYPE_TRANSFER=>ProductStock::TYPE_TRANSFER,
            ProductStock::TYPE_RECEIVED=>ProductStock::TYPE_RECEIVED,
        ];
    }

}
