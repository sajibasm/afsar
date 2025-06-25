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



<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <?php $form = ActiveForm::begin(['id' => 'formAjaxSellCreate']); ?>

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
                <?= $form->field($model, 'username')->textInput([
                    'maxlength' => true,
                    'readonly' => !empty($model->username)  // readonly if not empty
                ]) ?>
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
            <div class="col-md-12">
                <div class="form-group text-right" style="margin-top: 25px;">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-info', 'style' => 'margin-right:10px;']) ?>
                    <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

