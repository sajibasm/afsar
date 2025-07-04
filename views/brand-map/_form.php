<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BrandMap */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-map-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList([ 'Active' => 'Active', 'Inactive' => 'Inactive', ], ['prompt' => '']) ?>
        </div>
    </div>

    <div class="panel-footer">
        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' =>'btn btn-primary']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
