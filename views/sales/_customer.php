<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\models\PaymentType;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */

?>



<div class="sales-form">
    <?php $form = ActiveForm::begin([
        'id'=>'formAjaxSellCreate'
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'client_id')->widget(Select2::classname(), [
                'data' => CustomerUtility::getCustomerList(null, 'client_type DESC, client_name asc', true),
                'options' => [
                    'placeholder' => 'Select ... '
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= Html::activeHiddenInput($model, 'client_type'); ?>
            <?= $form->field($model, 'client_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'contact_number')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>




