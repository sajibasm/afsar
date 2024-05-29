<?php

use app\components\CommonUtility;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */

$this->title = Yii::t('app', 'Withdraw');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    var checkAvailable='".Url::base(true).'/'.Yii::$app->controller->id.'/check-available-product'."';
    var customerDetails='".Url::base(true).'/'.Yii::$app->controller->id.'/customer-details'."';
    ", View::POS_END, 'checkAvailableProduct'
);

$this->registerJsFile(Url::base(true).'/js/payment.js', ['depends'=>\yii\web\JqueryAsset::className()]);
//$this->registerJsFile(Url::base(true).'/js/salesDraftUpdate.js', ['depends'=>\yii\web\JqueryAsset::className()]);

$var = 'var type = {';

foreach(CommonUtility::getPaymentType() as $type){
    $var = $var." ".$type->payment_type_id.": '".$type->type."', ";
}
$var = rtrim($var, ', ');
$var=$var.' };';

$this->registerJs($var, View::POS_HEAD, 'paymentType');
$this->registerJsFile(Url::base(true).'/js/client-payment-withdraw.js', ['depends'=> JqueryAsset::className()]);

?>

<div class="client-paymenbt-withdraw">


    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $this->title ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">

            <?php $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'name')->textInput(['readOnly'=>true]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'remaining_amount')->textInput(['placeholder'=>'Type amount','readOnly'=>true]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'received_type')->textInput(['placeholder'=>'Type amount','readOnly'=>true]) ?>
                    </div>
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
                </div>



                <div class="row">
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
                    <div class="col-md-4">
                        <?= $form->field($model, 'remarks')->textInput(['placeholder'=>'Type Remarks']) ?>
                    </div>
                </div>


            <div class="modal-footer">
                <?= Html::submitButton(Yii::t('app', 'Withdraw'), ['class' =>'btn btn-success']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>



</div>