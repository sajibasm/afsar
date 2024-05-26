<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashBook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cash_in')->textInput() ?>

    <?= $form->field($model, 'cash_out')->textInput() ?>

    <?= $form->field($model, 'source')->dropDownList([ 'sales' => 'Sales', 'due-received' => 'Due-received', 'due-received-overflow' => 'Due-received-overflow', 'cash-hand-receieved' => 'Cash-hand-receieved', 'advance-sales' => 'Advance-sales', 'withdraw' => 'Withdraw', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'reference_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
