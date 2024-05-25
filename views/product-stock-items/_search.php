<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-stock-items-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'product_stock_items_id') ?>

    <?= $form->field($model, 'product_stock_id') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'brand_id') ?>

    <?= $form->field($model, 'size_id') ?>

    <?php // echo $form->field($model, 'cost_price') ?>

    <?php // echo $form->field($model, 'wholesale_price') ?>

    <?php // echo $form->field($model, 'retail_price') ?>

    <?php // echo $form->field($model, 'previous_quantity') ?>

    <?php // echo $form->field($model, 'new_quantity') ?>

    <?php // echo $form->field($model, 'total_quantity') ?>

    <?php // echo $form->field($model, 'alert_quantity') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
