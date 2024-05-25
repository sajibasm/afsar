<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\LcUtility;
use app\components\ProductUtility;
use app\components\SupplierUtility;
use app\components\WarehouseUtility;
use app\models\Brand;
use app\models\Item;
use app\models\ProductStock;
use app\models\Size;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-stock-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ProductUtility::getItemList(Item::STATUS_ACTIVE, 'item_name', true),
                'options' => [
                    'id'=>'stock_item_id',
                    'placeholder' => 'Select a items'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Item');
            ?>
        </div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'brand_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'stock_brand_id'],
                'pluginOptions'=>[
                    'depends'=>['stock_item_id'],
                    'placeholder' => 'Select a brand',
                    'url' => Url::to(['/product-stock/get-brand-list-by-item'])
                ]
            ])->label('Brand');
            ?>

        </div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'stock_size_id'],
                'pluginOptions'=>[
                    'depends'=>['stock_item_id','stock_brand_id'],
                    'placeholder' => 'Select a size',
                    'url' => Url::to(['/product-stock/get-size-list-by-brand'])
                ]
            ])->label('Size')
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' =>[ProductStock::TYPE_LOCAL=>ProductStock::TYPE_LOCAL, ProductStock::TYPE_IMPORT=>ProductStock::TYPE_IMPORT],
                'options' => ['placeholder' => 'Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'supplier')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => SupplierUtility::getSupplierList('name', true),
                'options' => ['placeholder' => 'Supplier'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'lc')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => LcUtility::getLcList('lc_name', true),
                'options' => ['placeholder' => 'LC'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'warehouse')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => WarehouseUtility::getWarehouseList('warehouse_name', true),
                'options' => ['placeholder' => 'Warehouse'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'total_quantity'); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'cost_price')->label('Cost'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'wholesale_price')->label('Wholesal'); ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'retail_price')->label('Retail'); ?>
        </div>
    </div>



    <div class="row">

        <div class="col-md-6">
            <?= $form->field($model, 'product_stock_id')->label('Stock Id') ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'created_at')->widget(
                DateRangePicker::className(),
                [
                    'attributeTo' => 'created_to',
                    'language' => 'en',
                    'size' => 'sm',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => SystemSettings::calenderDateFormat(),
                        'todayHighlight'=>true,
                        'endDate' => DateTimeUtility::getDate(null, SystemSettings::calenderEndDateFormat())
                    ]
                ]
            )
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>



