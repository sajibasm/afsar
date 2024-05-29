<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductItemsPrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-items-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'item_name')->textInput(['disabled'=>true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'brand_name')->textInput(['disabled'=>true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'size_name')->textInput(['disabled'=>true]) ?>

        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'cost_price')->textInput() ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'wholesale_price')->textInput() ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'retail_price')->textInput() ?>
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
