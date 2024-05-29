<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDetails */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-details-form">

    <?php $form = ActiveForm::begin([
        'id'=>'formReturn',
    ]); ?>

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
            <?= $form->field($model, 'quantity')->textInput() ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'sales_amount')->label('Unit Price')->textInput() ?>
        </div>


        <div class="col-md-4">
            <label class="control-label" for="salesdetails-item_id"></label>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Return'), ['class' =>'btn btn-info btn-block']) ?>
            </div>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
