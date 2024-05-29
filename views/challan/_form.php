<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Challan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="challan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'challan_id')->textInput() ?>

    <?= $form->field($model, 'sales_id')->textInput() ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'transport_id')->textInput() ?>

    <?= $form->field($model, 'transport_invoice_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'condition_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
