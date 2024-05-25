<?php

use app\components\CustomerUtility;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'client_name')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'client_mobile')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'sales_id')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'soldDate')->textInput(['readOnly'=>true]) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>


    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($account, 'total_amount')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($account, 'discount_amount')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($account, 'paid_amount')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($account, 'due_amount')->textInput(['readOnly'=>true]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
