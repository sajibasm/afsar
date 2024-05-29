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
       'id'=>'formReturnService'
   ]); ?>

    <div class="row">

        <div class="col-md-2">
            <?= $form->field($model, 'remarks')->textInput() ?>
        </div>


        <div class="col-md-2">
            <?= $form->field($model, 'due_amount')->textInput(['readOnly'=>true, 'id'=>'due-amount']) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'refund_amount')->textInput(['id'=>'service-refund']) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'cut_off_amount')->textInput(['id'=>'cut-off-amount', 'readOnly'=>true]) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'total_amount')->textInput(['readOnly'=>true, 'id'=>'total-amount']) ?>
            <?=Html::activeHiddenInput($model, 'maxRefundAmount', ['id'=>'maxRefundAmount'])?>
        </div>

        <div class="col-md-2">
            <label class="control-label" for="salesreturn-refund_amount"></label>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Return'), ['class' =>'btn btn-success btn-block']) ?>
            </div>
        </div>


    </div>
    <?php ActiveForm::end(); ?>


</div>