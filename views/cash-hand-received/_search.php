<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\DateWidget;
use app\components\OutletUtility;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashHandReceivedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-hand-received-search">


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
            <?= $form->field($model, 'received_amount')->label('Amount') ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <?php DateWidget::dateRange($model, $form, 'Date', 'created_at', 'created_to', false); ?>
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
