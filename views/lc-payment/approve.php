<?php

use app\components\CommonUtility;
use app\components\LcUtility;
use app\components\Utility;
use app\components\WarehouseUtility;
use app\models\PaymentType;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LcPayment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => 'Warehouse Payment',
    ]) . ' ' . $model->lc_payment_id;

?>
<div class="warehouse-payment-update">

    <?php $form = ActiveForm::begin() ?>

    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'lc_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(LcUtility::getLcList(), 'lc_id', 'lc_name'),
                'options' => ['placeholder' => 'Select a LC',  'disabled' => true],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'lc_payment_type')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(LcUtility::getLcPaymentType(), 'lc_payment_type_id', 'lc_payment_type_name'),
                'options' => ['placeholder' => 'Type',  'disabled' => true],
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
            echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
                'options' => [
                    'placeholder' => 'Select a type',
                     'disabled' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>

        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput(['readOnly' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => true, 'readOnly' => true]) ?>
        </div>
    </div>

    <?php if ($model->paymentType->type== PaymentType::TYPE_DEPOSIT): ?>

        <div class="row">
            <div class="col-md-6">
                <?php
                echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(CommonUtility::getBank(), 'bank_id', 'bank_name'),
                    'options' => [
                        'id' => 'bank_id',
                        'placeholder' => 'Select a bank',
                        'disabled' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <?php
                echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(CommonUtility::getBranchByBankId($model->bank_id), 'branch_id', 'branch_name'),
                    'options' => [
                        'id' => 'branch_id',
                        'disabled' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>

        </div>

    <?php endif; ?>


    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'LCPaymentpjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->lc_payment_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    
