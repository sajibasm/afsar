<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDraft */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-draft-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sales_id')->textInput() ?>

    <?= $form->field($model, 'item_id')->textInput() ?>

    <?= $form->field($model, 'brand_id')->textInput() ?>

    <?= $form->field($model, 'size_id')->textInput() ?>

    <?= $form->field($model, 'cost_amount')->textInput() ?>

    <?= $form->field($model, 'sales_amount')->textInput() ?>

    <?= $form->field($model, 'total_amount')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'challan_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'challan_quantity')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'insert' => 'Insert', 'update' => 'Update', 'return' => 'Return', 'update-added' => 'Update-added', 'update-deleted' => 'Update-deleted', 'sales-pending' => 'Sales-pending', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
