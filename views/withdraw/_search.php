<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\DateWidget;
use app\components\OutletUtility;
use app\models\Withdraw;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WithdrawSearch */
/* @var $form yii\widgets\ActiveForm */

$exportFileName = 'cash_hand_received_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>


<div class="withdraw-search">

    <?php $form = ActiveForm::begin([
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
            echo $form->field($model, 'type')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => [Withdraw::TYPE_CASH => Withdraw::TYPE_CASH, Withdraw::TYPE_DEPOSIT => Withdraw::TYPE_DEPOSIT],
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
            <?= $form->field($model, 'withdraw_amount')->label('Amount') ?>
        </div>
        <div class="col-md-6">
            <?php
                DateWidget::dateRange($model, $form, 'Date', 'created_at', 'created_to', false);
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
