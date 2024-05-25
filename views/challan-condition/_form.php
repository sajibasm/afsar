<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ChallanCondition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="challan-condition-form">


    <div class="panel panel-primary">

        <div class="panel-heading">
            Challan Condition
        </div>

        <?php $form = ActiveForm::begin() ?>

        <div class="panel-body">
            <div class="brand-form">

                <?= $form->field($model, 'challan_condition_name')->textarea(['maxlength' => true]) ?>

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

</div>
