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
            <div class="col-md-6">
                <?= $form->field($model, 'client_name')->textInput(['readOnly'=>true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sales_id')->textInput(['readOnly'=>true]) ?>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <?= $form->field($model, 'reconciliation_amount')->textInput(['readOnly'=>true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sales_return_amount')->textInput(['readOnly'=>true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'paid_amount')->label('Paid')->textInput(['readOnly'=>true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'discount_amount')->textInput(['readOnly'=>true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'due_amount')->textInput(['readOnly'=>true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'total_amount')->textInput(['readOnly'=>true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'received_amount')->textInput(['readOnly'=>true]) ?>
            </div>

        </div>

    <?php ActiveForm::end(); ?>

</div>
