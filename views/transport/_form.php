<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Transport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-form">
     <?php $form = ActiveForm::begin() ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'transport_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'transport_address')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'transport_contact_person')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'transport_contact_number')->textInput(['maxlength' => true]) ?>
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
