<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'sales_details_id') ?>

    <?= $form->field($model, 'sales_id') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'brand_id') ?>

    <?= $form->field($model, 'size_id') ?>

    <?php // echo $form->field($model, 'unit') ?>

    <?php // echo $form->field($model, 'cost_amount') ?>

    <?php // echo $form->field($model, 'sales_amount') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'challan_unit') ?>

    <?php // echo $form->field($model, 'challan_quantity') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
