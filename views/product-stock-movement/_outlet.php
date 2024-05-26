<?php


use app\components\OutletUtility;
use aryelds\sweetalert\SweetAlert;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockOutlet */
/* @var $form yii\widgets\ActiveForm */
$this->title = "Source Outlet";
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
                     echo $form->field($model, 'transferOutlet')->widget(Select2::classname(), [
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





