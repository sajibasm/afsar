<?php

namespace app\models;

use app\components\DateTimeUtility;
use app\components\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "product_stock_outlet".
 *
 * @property int $product_stock_outlet_id
 * @property string $product_stock_outlet_code
 * @property string $product_stock_id
 * @property string $invoice
 * @property string $ref
 * @property string|null $note
 * @property string $type
 * @property string|null $remarks
 * @property string|null $params
 * @property int $transferOutlet
 * @property string $transferFrom
 * @property string $receivedFrom
 * @property int $receivedOutlet
 * @property int|null $transferBy
 * @property int|null $receivedBy
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $status
 */
class ProductStockOutlet extends \yii\db\ActiveRecord
{

    const TRANSFER_FROM_OUTLET = 'Outlet';
    const TRANSFER_FROM_STOCK = 'Stock';

    const TYPE_TRANSFER = 'Transfer';
    const TYPE_RECEIVED = 'Received';
    const TYPE_MOVEMENT = 'Movement';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'reject';

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => function() { return DateTimeUtility::getDate(null, 'Y-m-d H:i:s', 'Asia/Dhaka'); }
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_stock_outlet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_stock_outlet_code', 'invoice'], 'required'],
            [['type', 'params', 'status'], 'string'],
            [['transferOutlet', 'receivedOutlet', 'transferBy', 'receivedBy'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['transferFrom', 'receivedFrom'], 'string', 'max' => 20],
            [['product_stock_outlet_code'], 'string', 'max' => 50],
            [['invoice'], 'string', 'max' => 20],
            [['note'], 'string', 'max' => 255],
            [['remarks'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_stock_outlet_id' => 'Stock Outlet ID',
            'product_stock_outlet_code' => 'Stock Outlet Code',
            'invoice' => 'Invoice',
            'Ref' => 'Ref',
            'note' => 'Note',
            'type' => 'Type',
            'remarks' => 'Remarks',
            'params' => 'Params',
            'transferOutlet' => 'Transfer',
            'transferFrom' => 'Origin',
            'receivedFrom' => 'Destination',
            'receivedOutlet' => 'Received',
            'transferBy' => 'Transfer By',
            'receivedBy' => 'Received By',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'status' => 'Status',
        ];
    }


    public static function saveOutletStock($previousId, ProductStock $productStock, $requestedData, $isDeleteItems)
    {
        $invoiceId = 1;
        $stockOutlet =  ProductStockOutlet::find()->orderBy('product_stock_outlet_id DESC')->one();
        if($stockOutlet){ $invoiceId = $stockOutlet->product_stock_outlet_id;}
        $productStockOutlet = new self();
        $productStockOutlet->product_stock_outlet_code = uniqid(rand(1, 9999));
        $productStockOutlet->invoice = $productStock->invoice_no = Utility::genInvoice($invoiceId,'STR-');
        $productStockOutlet->ref = $productStock->product_stock_id;
        $productStockOutlet->note = $productStock->remarks;
        $productStockOutlet->type = ProductStockOutlet::TYPE_RECEIVED;
        $productStockOutlet->remarks = $productStock->remarks;
        $productStockOutlet->params = $productStock->params;
        $productStockOutlet->transferFrom = ProductStockOutlet::TRANSFER_FROM_STOCK;
        $productStockOutlet->transferOutlet = -1;
        $productStockOutlet->receivedFrom = ProductStockOutlet::TRANSFER_FROM_OUTLET;
        $productStockOutlet->receivedOutlet = $requestedData['ProductStock']['outlet'];
        $productStockOutlet->transferBy = Yii::$app->user->getId();
        $productStockOutlet->status = ProductStockOutlet::STATUS_PENDING;
        if ($productStockOutlet->save()){
            $isDraftToOutletSave = ProductStockItemsOutlet::draftToOutlet($productStockOutlet->product_stock_outlet_id, $isDeleteItems, $productStockOutlet->transferOutlet, $productStockOutlet->receivedOutlet);
            $isDraftToStatementUpdate = ProductStockItemsOutlet::draftToStatementUpdate($productStock->product_stock_id, $isDeleteItems, $productStockOutlet->invoice);
            $isDeleteItems = ProductStockItemsDraft::deleteAll(['product_stock_id'=>$previousId]);
            if($isDraftToOutletSave && $isDraftToStatementUpdate && $isDeleteItems){
                $params = Json::decode($productStock->params);
                $params['outletStock'] = $productStockOutlet->product_stock_outlet_id;
                $productStock->params = Json::encode($params);
                if($productStock->save()){
                    return true;
                }
            }
        }
        return false;
    }


    public function getProductItems()
    {
        return $this->hasMany(ProductStockItemsOutlet::className(), ['product_stock_outlet_id' => 'product_stock_outlet_id']);
    }

    public function getProductStock()
    {
        return $this->hasOne(ProductStock::className(), ['product_stock_id' => 'product_stock_id']);
    }


    public function getReceivedByUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'receivedBy']);
    }

    public function getTransferByUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'transferBy']);
    }

    public function getReceivedOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'receivedOutlet']);
    }

    public function getTransferOutletDetail()
    {
        return $this->hasOne(Outlet::className(), ['outletId' => 'transferOutlet']);
    }

}
