<?php

use app\components\CommonUtility;
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
/* @var $model app\models\WarehousePayment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => 'Warehouse Payment',
    ]) . ' ' . $model->id;

?>
<div class="warehouse-payment-update">

    <?php $form = ActiveForm::begin() ?>

    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'warehouse_id')->widget(Select2::classname(), array(
                'data' => ArrayHelper::map(WarehouseUtility::getWarehouseList(), 'warehouse_id', 'warehouse_name'),
                'options' => [
                        'placeholder' => 'Select a Warehouse',
                        'disabled'=>true
                ],
                'pluginOptions' => array(
                    'allowClear' => true
                ),
            ));
            ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'payment_amount')->textInput(['readOnly' => true]) ?>

        </div>

    </div>

    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'month')->widget(Select2::classname(), array(
                'data' => CommonUtility::getMonth(),
                'options' => [
                    'placeholder' => 'Select month',
                    'disabled' => true
                ],
                'pluginOptions' => array(
                    'allowClear' => true
                ),
            ));
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'year')->widget(Select2::classname(), array(
                'data' => CommonUtility::getYear(),
                'options' => [
                    'placeholder' => 'Select year',
                    'disabled' => true
                ],

                'pluginOptions' => array(
                    'allowClear' => true

                ),
            ));
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
            <?= $form->field($model, 'remarks')->textInput(['readOnly' => true]) ?>
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
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary float-right', 'id'=>'approveConfirmation', 'data-view'=>'warehousePaymentpjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    
