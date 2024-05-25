<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sales_id')->textInput() ?>

    <?= $form->field($model, 'memo_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'sales' => 'Sales', 'return' => 'Return', 'repair' => 'Repair', 'service' => 'Service', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'debit')->textInput() ?>

    <?= $form->field($model, 'credit')->textInput() ?>

    <?= $form->field($model, 'balance')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
