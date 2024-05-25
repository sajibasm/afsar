<?php

use app\components\OutletUtility;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashHandReceived */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-hand-received-form">



        <?php $form = ActiveForm::begin() ?>

        <div class="row">
            <div class="col-md-4">
                <?php
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => OutletUtility::getUserOutlet(),
                    'options' => [
                        'placeholder' => 'Outlet '
                    ]
                ]);
                ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'received_amount')->textInput() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="panel-footer">
            <div class="modal-footer">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
