<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\OutletUtility;
use app\components\Utility;
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
$this->title = 'Sell Outlet';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body" id="sales_product_details">
        <?php $form = ActiveForm::begin(['id'=>'formAjaxSellCreate']); ?>
        <div class="row">
            <div class="col-md-12">
                <?php
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => OutletUtility::getUserOutlet(),
                    'options' => [
                        'placeholder' => 'Select From Outlet'
                    ]
                ])->label('Transfer Outlet');
                ?>
            </div>
        </div>

        <div class="panel-footer">
            <div class="modal-footer">
                <?= Html::submitButton( Yii::t('app', 'Next'),  ['class' => 'btn btn-info']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>