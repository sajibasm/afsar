<?php

use app\components\CustomerUtility;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <?= Html::activeHiddenInput($model, 'client_type'); ?>
            <?= $form->field($model, 'client_name')->textInput(['maxlength' => true, 'readOnly'=>true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'client_contact_number')->textInput(['maxlength' => true, 'readOnly'=>true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'readOnly'=>true]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

