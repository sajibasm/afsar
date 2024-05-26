<?php

use app\components\SystemSettings;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\LcUtility;
use app\components\OutletUtility;
use app\components\ProductUtility;
use app\components\SupplierUtility;
use app\components\WarehouseUtility;
use app\models\Brand;
use app\models\BrandNew;
use app\models\Client;
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
        'action' => ['product-brand'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'options' => [
                    'id' => 'outlet_id',
                    'placeholder' => 'Outlet'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'client_id')->widget(DepDrop::classname(), [
                //'theme'=>Select2::THEME_DEFAULT,
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                //'options' => ['id' => 'brand_id'],
                'pluginOptions' => [
                    'depends' => ['outlet_id'],
                    'placeholder' => 'Select Customer',
                    'url' => Url::to(['/client/by-outlet'])
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <?php
            echo $form->field($model, 'brand_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ProductUtility::getBrandListMap(BrandNew::STATUS_ACTIVE, 'name', true),
                'options' => [
                    'id'=>'stock_brand_map_id',
                    'placeholder' => 'Select a Brand'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Brand');
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?php
                echo $form->field($model, 'item_id')->widget(DepDrop::classname(), [
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                    'options' => ['id'=>'stock_item_id'],
                    'pluginOptions'=>[
                        'depends'=>['stock_brand_map_id'],
                        'placeholder' => 'Select a Item',
                        'url' => Url::to(['/product-stock/get-item-by-brand'])
                    ]
                ])->label('Item')
                ?>
        </div>

        <div class="col-md-6">
            <?php
                echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'stock_size_id'],
                'pluginOptions'=>[
                    'depends'=>['stock_item_id','stock_brand_map_id'],
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
            echo $form->field($model, 'sortingBy')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => [1=>'Time', -1=>'Quantity'],
                'options' => ['placeholder' => 'Sorting'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
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



