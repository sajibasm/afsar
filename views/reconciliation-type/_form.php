<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReconciliationType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receoncliation-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'show_invoice')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => [ 'yes' => 'Yes', 'no' => 'No'],
                'options' => ['placeholder' => 'Select '],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'status')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => [ 'Active' => 'Active', 'Inactive' => 'Inactive'],
                'options' => ['placeholder' => 'Select Status'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>

    <div class="panel-footer">
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center">
                    <?= \app\components\ButtonHelper::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
