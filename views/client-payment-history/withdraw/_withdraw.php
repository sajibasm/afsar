<?php

use app\components\CommonUtility;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\widgets\DepDrop;


/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistory */
/* @var $form yii\widgets\ActiveForm */


$var = 'var type = {';

foreach(CommonUtility::getPaymentType() as $type){
    $var = $var." ".$type->payment_type_id.": '".$type->type."', ";
}
$var = rtrim($var, ', ');
$var=$var.' };';

$this->registerJs($var, View::POS_HEAD, 'paymentType');

$this->registerJsFile(Url::base(true).'/js/client-payment-withdraw.js', ['depends'=> JqueryAsset::className()]);
?>


    <div class="client-payment-history-form">
    <?php $form = ActiveForm::begin(); ?>

        <div class="row">

            <div class="col-md-4">
                <?php
                echo $form->field($model, 'payment_type_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
                    'options' => [
                        'id'=>'payment_type',
                        'placeholder' => 'Select a type'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>



            <div class="col-md-4">
                <?php
                echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getBank(), 'bank_id', 'bank_name'),
                    'options' => [
                        'id'=>'bank_id',
                        'placeholder' => 'Select a bank',
                        'disabled'=>true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>


            <div class="col-md-4">
                <?php
                echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT,],
                    'options' => ['id'=>'branch_id'],
                    'pluginOptions'=>[
                        'depends'=>['bank_id'],
                        'placeholder' => 'Select a branch',
                        'url' => Url::to(['/bank/get-branch'])
                    ]
                ]);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'remarks')->textInput(['placeholder'=>'Type Remarks']) ?>
            </div>
            <div class="col-md-6">
                    <label for="clientpaymenthistory" class="control-label"></label>
                   <?= Html::submitButton(Yii::t('app', 'Withdraw'), ['class' =>'btn btn-info btn-block']) ?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>
    </div>






