<?php

use app\components\CommonUtility;
use app\components\EmployeeUtility;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalaryHistory */
/* @var $form yii\widgets\ActiveForm */
?>


   <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'employee_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(EmployeeUtility::getEmployeeList(), 'id', 'full_name'),
                'options' => ['placeholder' => 'Select an employee'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'month')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'showToggleAll' => false,
                'data' => CommonUtility::getMonth(),
                'maintainOrder' => true,
                'options' => [
                    'placeholder' => 'Select a month ',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'year')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => CommonUtility::getYear(),
                'maintainOrder' => true,
                'options' => [
                    'placeholder' => 'Select a year '
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
                'options' => [
                    'placeholder' => 'Select a type'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-4">
            <?php
            echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getBank(), 'bank_id', 'bank_name'),
                'options' => [
                    'id'=>'bank_id',
                    'placeholder' => 'Select a bank'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-4">
            <?php
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true],   'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'branch_id'],
                'pluginOptions'=>[
                    'depends'=>['bank_id'],
                    'placeholder' => 'Select a branch',
                    'url' => Url::to(['/bank/get-branch'])
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'withdraw_amount')->textInput(['readOnly'=>true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'remaining_salary')->textInput() ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="panel-footer">
        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
        </div>
    </div>

   <?php ActiveForm::end(); ?>
