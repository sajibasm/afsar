<?php

use app\components\ConstrainUtility;
use kartik\password\PasswordInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'email')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'password_hash')->widget(PasswordInput::classname(), [
                'pluginOptions' => [
                    'showMeter' => true,
                    'toggleMask' => false
                ]
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'status')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ConstrainUtility::USER_STATUS_LIST,
                'pluginOptions' => [
                    'disabled' => false
                ],
                'options' => [
                    'placeholder' => 'Status '
                ]
            ]);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
        </div>
    </div>


    <div class="panel-footer">
        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-info']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
