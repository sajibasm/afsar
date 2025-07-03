<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\ProductUtility;
use app\components\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
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

    public function beforeSave($insert)
    {
        // Trim all string inputs
        foreach ($this->attributes as $attribute => $value) {
            if (is_string($value)) {
                $this->$attribute = trim($value);
            }
        }

        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Your custom logic for new records
        if ($this->isNewRecord) {
            if ($this->type == ProductStock::TYPE_LOCAL) {
                $this->lc_id = null;
                $this->warehouse_id = null;
            } else {
                $this->buyer_id = null;
            }
        }

        return true;
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
            'outlet' => Yii::t('app', 'Store'),
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

    /**
     * @param ProductStock $model
     * @param $productStockItemsDraft
     * @return false|int
     * @throws Exception
     */
    public static function saveToInventory(ProductStock $model, $productStockItemsDraft)
    {
        $items = [];
        $statements = [];
        $newPrices = [];

        $isSave = true;

        foreach ($productStockItemsDraft as $draft) {
            $currentQty = ProductUtility::getTotalQuantity($draft->size_id);
            $newQty = $draft->new_quantity;
            $totalQty = $currentQty + $newQty;

            // Prepare stock items
            $items[] = [
                $model->product_stock_id,
                $draft->item_id,
                $draft->brand_id,
                $draft->size_id,
                $draft->cost_price,
                $draft->wholesale_price,
                $draft->retail_price,
                $currentQty,
                $newQty,
                $totalQty,
                ProductStockItems::STATUS_DONE
            ];

            // Prepare statement
            $statements[] = [
                $draft->item_id,
                $draft->brand_id,
                $draft->size_id,
                $newQty,
                ProductStatement::TYPE_STOCK,
                'success',
                $model->product_stock_id,
                Yii::$app->user->getId()
            ];

            // Handle price update or insert
            $existingPrice = ProductItemsPrice::find()->where(['size_id' => $draft->size_id])->one();
            if ($existingPrice) {
                $existingPrice->setAttributes([
                    'cost_price' => $draft->cost_price,
                    'wholesale_price' => $draft->wholesale_price,
                    'retail_price' => $draft->retail_price,
                    'quantity' => $newQty,
                    'alert_quantity' => $draft->alert_quantity
                ]);
                if (!$existingPrice->save()) {
                    $isSave = false;
                }
            } else {
                $newPrices[] = [
                    $draft->item_id,
                    $draft->brand_id,
                    $draft->size_id,
                    $draft->cost_price,
                    $draft->wholesale_price,
                    $draft->retail_price,
                    $newQty,
                    $newQty,
                    $draft->alert_quantity
                ];
            }
        }

        if (empty($items) || !$isSave) {
            return false;
        }

        // Insert stock items
        $insertedItems = Yii::$app->db->createCommand()->batchInsert(ProductStockItems::tableName(), [
            'product_stock_id', 'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price',
            'previous_quantity', 'new_quantity', 'total_quantity', 'status'
        ], $items)->execute();

        if ($insertedItems !== count($items)) {
            return false;
        }

        // Insert statements
        $insertedStatements = Yii::$app->db->createCommand()->batchInsert(ProductStatement::tableName(), [
            'item_id', 'brand_id', 'size_id', 'quantity', 'type', 'remarks', 'reference_id', 'user_id'
        ], $statements)->execute();

        if ($insertedStatements !== count($statements)) {
            return false;
        }

        // Insert new prices if any
        if (!empty($newPrices)) {
            $insertedPrices = Yii::$app->db->createCommand()->batchInsert(ProductItemsPrice::tableName(), [
                'item_id', 'brand_id', 'size_id', 'cost_price', 'wholesale_price', 'retail_price', 'quantity', 'total_quantity', 'alert_quantity'
            ], $newPrices)->execute();

            if ($insertedPrices !== count($newPrices)) {
                return false;
            }
        }

        // Final cleanup
        return true;
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

    public static function saveToStoreInventory(ProductStock $productStock, $requestedData, $isDeleteItems, $outlet)
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

    public static function getStatusList(){
        return [
            ProductStock::STATUS_ACTIVE=>ucfirst(ProductStock::STATUS_ACTIVE),
            ProductStock::STATUS_INACTIVE=>ucfirst(ProductStock::STATUS_INACTIVE),
            ProductStock::STATUS_PENDING=>ucfirst(ProductStock::STATUS_PENDING),
            ProductStock::STATUS_REJECT=>ucfirst(ProductStock::STATUS_REJECT),
        ];
    }

    public static function getTypeList(){
        return [
            ProductStock::TYPE_LOCAL=>ucfirst(ProductStock::TYPE_LOCAL),
            ProductStock::TYPE_IMPORT=>ucfirst(ProductStock::TYPE_IMPORT),
            ProductStock::TYPE_MOVEMENT=>ucfirst(ProductStock::TYPE_MOVEMENT),
            ProductStock::TYPE_TRANSFER=>ucfirst(ProductStock::TYPE_TRANSFER),
            ProductStock::TYPE_RECEIVED=>ucfirst(ProductStock::TYPE_RECEIVED),
        ];
    }

}
