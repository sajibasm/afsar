<?php

namespace app\models;

use Imagine\Image\Box;
use Yii;
use yii\helpers\Url;
use yii\imagine\Image;

/**
 * This is the model class for table "{{%size}}".
 *
 * @property integer $size_id
 * @property integer $brand_id
 * @property integer $item_id
 * @property string $size_name
 * @property string $size_image
 * @property string $size_description
 * @property integer $unit
 * @property string $unit_quantity
 * @property string $lowest_price
 * @property string $size_status
 *
 * @property MarketBookSalesDetails[] $marketBookSalesDetails
 * @property ProductStatement[] $productStatements
 * @property ProductStockItems[] $productStockItems
 * @property ProductStockItemsDraft[] $productStockItemsDrafts
 * @property ProductStockStatement[] $productStockStatements
 * @property SalesDetails[] $salesDetails
 * @property SalesDraftItems[] $salesDraftItems
 * @property Brand $brand
 * @property ProductUnit $productUnit
 * @property Item $item
 */
class Size extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    public $imageFile;

    public $status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%size}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'item_id', 'size_name', 'size_status', 'unit', 'unit_quantity'], 'required'],
            [['brand_id', 'item_id'], 'integer'],
            [['size_name'], 'string', 'max' => 50],
            [['size_status'], 'string', 'max' => 8],
            [['size_description'], 'string'],
            [['size_image'], 'string', 'max' => 50],
            [['unit_quantity', 'lowest_price'], 'number'],
            //['uni_quantity', 'compare', 'compareValue'=>0.1],
            ['size_name', 'unique', 'targetAttribute' => ['item_id', 'brand_id', 'size_name']],
            [['imageFile'], 'file', 'skipOnEmpty' =>true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'size_id' => Yii::t('app', 'Size'),
            'brand_id' => Yii::t('app', 'Brand'),
            'item_id' => Yii::t('app', 'Item'),
            'size_name' => Yii::t('app', 'Size'),
            'size_image' => Yii::t('app', 'Image'),
            'size_description' => Yii::t('app', 'Product Details'),
            'unit' => Yii::t('app', 'Unit'),
            'unit_quantity' => Yii::t('app', 'Unit Qty'),
            'lowest_price' => Yii::t('app', 'Lowest Price(%)'),
            'size_status' => Yii::t('app', 'Status'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketBookSalesDetails()
    {
        return $this->hasMany(MarketBookSalesDetails::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductUnit()
    {
        return $this->hasOne(ProductUnit::className(), ['id' => 'unit']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStatements()
    {
        return $this->hasMany(ProductStatement::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItems()
    {
        return $this->hasMany(ProductStockItems::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockItemsDrafts()
    {
        return $this->hasMany(ProductStockItemsDraft::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStockStatements()
    {
        return $this->hasMany(ProductStockStatement::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDetails()
    {
        return $this->hasMany(SalesDetails::className(), ['size_id' => 'size_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesDraftItems()
    {
        return $this->hasMany(SalesDraftItems::className(), ['size_id' => 'size_id']);
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
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
    }

    public function removeFile($file)
    {
        $origin = Yii::getAlias('@webroot/uploads/'.$file);
        $thumb = Yii::getAlias('@webroot/uploads/thumb/'.$file);

        if($this->existsFile($origin)){
            unlink($origin);
        }else{
            return false;
        }

        if($this->existsFile($thumb)){
            unlink($thumb);
        }else{
            return false;
        }

        return true;

    }

    public function saveThumb()
    {
        $file = Yii::getAlias('@webroot/uploads/'.$this->size_image);
        $savePath = Yii::getAlias('@webroot/uploads/thumb/'.$this->size_image);
        Image::getImagine()->open($file)->thumbnail(new Box(120, 120))->save($savePath , ['quality' => 100]);
    }

    public function upload()
    {
        if ($this->validate()) {
            if(isset($this->imageFile->extension)){
                $this->size_image = Yii::$app->security->generateRandomString(). '.' . $this->imageFile->extension;
                $this->imageFile->saveAs('uploads/'.$this->size_image);
                $this->imageFile = null;
                $this->saveThumb();
            }
            return true;
        } else {
            return false;
        }

    }

    public function existsFile($file = null)
    {
        if (file_exists($file)) {
            return true;
        }

/*        mkdir('@webroot/uploads', 0777, true);
        mkdir('@webroot/uploads/thumb', 0777, true);*/


        return false;
    }

    public function getImageUrl($origin = false)
    {
        if($origin){
            $url =  Url::base(true).'/uploads/';
        }else{
            $url =  Url::base(true).'/uploads/thumb/';
        }

        if($this->size_image!=''){
            if ($this->existsFile(Yii::getAlias('@webroot/uploads/thumb/').$this->size_image)) {
                return $url.$this->size_image;
            }
        }

        return $url.'no-image.jpg';
    }
}
