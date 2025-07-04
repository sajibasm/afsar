<?php

use app\components\ExpenseUtility;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ExpenseType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expense-type-form">



        <?php $form = ActiveForm::begin() ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'expense_type_name')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'expense_type_status')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ExpenseUtility::getExpenseTypeStatus(),
                    'options' => ['placeholder' => 'Select Status'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
        </div>

        <div class="panel-footer">
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end align-items-center">
                        <?= \app\components\ButtonHelper::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                            'type' => 'submit',
                            'class' => 'btn btn-primary',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
