<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\ProductUtility;
use app\models\Client;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistory */
/* @var $form yii\widgets\ActiveForm */
?>


    <div class="client-payment-history-form">
    <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'name')->textInput(['readOnly'=>true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?php
                echo $form->field($model, 'payment_type_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
                    'options' => [
                        'placeholder' => 'Select a type',
                        'disabled'=>true
                    ],
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'remaining_amount')->textInput(['placeholder'=>'Type amount','readOnly'=>true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <?php
                echo $form->field($model, 'payType')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => $model->getPaymentMode(),
                    'options' => [
                        'placeholder' => 'Select a type',
                    ],
                ]);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php
                echo $form->field($model, 'invoices')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(ProductUtility::getInvoiceHasDue($model->client_id), 'sales_id', 'sales_id'),
                    'options' => [
                        'placeholder' => 'Select Invoice',
                        'multiple'=>true,
                        'disabled'=>true
                    ],
                ]);
                ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'remarks')->textInput(['readOnly'=>true]) ?>
            </div>
        </div>


        <div class="modal-footer">
            <?= Html::submitButton(Yii::t('app', 'Adjust Payment'), ['class' =>'btn btn-success']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
        </div>


    <?php ActiveForm::end(); ?>
    </div>






