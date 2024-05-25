<?php

use app\components\CommonUtility;
use app\components\DateWidget;
use app\components\OutletUtility;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BankReconciliationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-reconciliation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
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
            <?= $form->field($model, 'amount'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data'=> CommonUtility::getPaymentType(true),
                'options' => ['placeholder' => 'Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'id'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'invoice_id'); ?>
        </div>

        <div class="col-md-6">
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
