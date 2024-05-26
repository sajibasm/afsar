<?php

use app\components\EmployeeUtility;
use app\models\Employee;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalaryHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="salary-history-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'withdraw_amount') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'remaining_salary') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'month') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'year') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'employee_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => EmployeeUtility::getEmployeeList('full_name',  Employee::ACTIVE_STATUS, true),
                'options' => ['placeholder' => 'Employee'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
