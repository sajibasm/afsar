<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-form">

    <?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'city_name')->textInput(['maxlength' => true]) ?>
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
