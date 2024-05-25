<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\DateWidget;
use app\components\ExpenseUtility;
use app\components\OutletUtility;
use app\models\Expense;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ExpenseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expense-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>

    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'pluginOptions' => [
                    'disabled' => false
                ],
                'options' => [
                    'placeholder' => 'Outlet '
                ]
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'expense_type_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' =>ExpenseUtility::getExpenseList('expense_type_name', true),
                'options' => ['placeholder' => 'Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' =>[Expense::TYPE_CASH=>Expense::TYPE_CASH, Expense::TYPE_DEPOSIT=>Expense::TYPE_DEPOSIT],
                'options' => ['placeholder' => 'Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'expense_id'); ?>
        </div>

    </div>


    <?php if(Yii::$app->controller->id=='reports'): ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'expense_amount'); ?>
            </div>
            <div class="col-md-6">
                <?php
                echo $form->field($model, 'created_at')->widget(
                    DateRangePicker::className(),
                    [
                        'attributeTo' => 'created_to',
                        'language' => 'en',
                        'size' => 'ms',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => SystemSettings::calenderDateFormat(),
                            'todayHighlight'=>true,
                            'endDate' => DateTimeUtility::getDate(null, SystemSettings::calenderEndDateFormat())

                        ]
                    ]
                )
                ?>
            </div>
        </div>

    <?php else:?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'expense_amount'); ?>
            </div>
        </div>

    <?php endif;?>


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
