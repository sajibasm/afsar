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
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

</div>
