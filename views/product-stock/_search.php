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
            echo $form->field($model, 'type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' =>ProductStock::getTypeList(),
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
            <?= $form->field($model, 'product_stock_id')->label('Stock Id') ?>
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
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>



