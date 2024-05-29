<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerWithdraw */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-withdraw-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'payment_history_id')->textInput(['disabled'=>!$model->isNewRecord?true:false]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => true, 'disabled'=>!$model->isNewRecord?true:false]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'extra')->textInput(['maxlength' => true, 'disabled'=>!$model->isNewRecord?true:false]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'amount')->textInput() ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'status')->textInput(['maxlength' => true, 'disabled'=>!$model->isNewRecord?true:false]) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'onClick'=>$model->isNewRecord?"return confirm('Do you want to create new payment?')":"return confirm('Do you want to update payment?')"]) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
