<?php

use app\components\CustomerUtility;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\SalesReturn */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="sales-form">

   <?php $form = ActiveForm::begin([
       'id'=>'formSalesReturn'
   ]); ?>


    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'remarks')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'refund_amount')->textInput(['readOnly'=>true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'cut_off_amount')->textInput(['readOnly'=>true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'due_amount')->textInput(['readOnly'=>true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'total_amount')->label('Total')->textInput(['readOnly'=>true]) ?>
        </div>
    </div>



    <div class="row">
        <div class="col-md-12 text-right">
            <div class="form-group" style="margin-top: 25px;">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default', 'style' => 'margin-right:10px;']) ?>
            </div>
        </div>
    </div>



    <?php ActiveForm::end(); ?>
</div>
