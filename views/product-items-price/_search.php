<?php

use app\components\ProductUtility;
use app\models\Item;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductItemsPriceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-items-price-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


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

    <?php echo  $form->field($model, 'cost_price') ?>

    <?php // echo $form->field($model, 'wholesale_price') ?>

    <?php // echo $form->field($model, 'retail_price') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'total_quantity') ?>

    <?php // echo $form->field($model, 'alert_quantity') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
