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

        <div class="col-md-4">
            <?= $form->field($model, 'name')->textInput(['readOnly'=>true]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'remaining_amount')->textInput(['placeholder'=>'Type amount','readOnly'=>true]) ?>
        </div>


        <div class="col-md-4">
            <?= $form->field($model, 'received_type')->textInput(['placeholder'=>'Type amount','readOnly'=>true]) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
    </div>






