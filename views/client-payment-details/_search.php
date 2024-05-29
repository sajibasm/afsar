<?php

use app\components\CustomerUtility;
use app\components\OutletUtility;
use app\models\Client;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-payment-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'GET',
    ]); ?>


    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'options' => [
                    'id' => 'outlet_id',
                    'placeholder' => 'Outlet'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'client_id')->widget(DepDrop::classname(), [
                //'theme'=>Select2::THEME_DEFAULT,
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                //'options' => ['id' => 'brand_id'],
                'pluginOptions' => [
                    'depends' => ['outlet_id'],
                    'placeholder' => 'Select Customer',
                    'url' => Url::to(['/client/by-outlet'])
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'payment_history_id') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sales_id') ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'paid_amount') ?>

    <?php // echo $form->field($model, 'payment_type') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

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
