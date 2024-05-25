<?php

use app\components\CommonUtility;
use app\components\ProductUtility;
use app\components\Utility;
use app\models\ProductStockItemsDraft;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */
/* @var $productStock app\models\ProductStock */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="product-stock-items-draft-form">
    <?php $form = ActiveForm::begin([
        'id' => 'formAjaxStock',
    ]); ?>

    <div id="errorSummary" class="error-summary" style="display: none"></div>

    <div class="row">
        <div class="col-sm-4">
            <?php
            echo Html::activeHiddenInput($model, 'totalQuantity');

            echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ProductUtility::getItemList(), 'item_id', 'item_name'),
                'options' => [
                    'id' => 'item_id',
                    'placeholder' => 'Select a Items'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);;
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            echo $form->field($model, 'brand_id')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                'options' => ['id' => 'brand_id'],
                'pluginOptions' => [
                    'depends' => ['item_id'],
                    'placeholder' => 'Select Brand',
                    'url' => Url::to(['/product-stock/get-brand-list-by-item'])
                ]
            ]);;
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                'options' => ['id' => 'size_id'],
                'pluginOptions' => [
                    'depends' => ['item_id', 'brand_id'],
                    'placeholder' => 'Select Size',
                    'url' => Url::to(['/product-stock/get-size-list-by-brand'])
                ]
            ]);;
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'cost_price')->textInput(['placeholder' => 'Cost Price']); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'wholesale_price')->textInput(['placeholder' => 'Wholesale Price']); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'retail_price')->textInput(['placeholder' => 'Retail Price']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'new_quantity')->textInput(['placeholder' => 'Quantitiy']); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'alert_quantity')->textInput(['placeholder' => 'Alert Quantity']); ?>
        </div>

        <div class="col-sm-4">
            <label class="control-label" for="productstockitemsdraft-new_quantity"></label>
            <?= Html::submitButton(Yii::t('app', 'Add2Cart'), ['class' => 'btn btn-primary btn-block']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>


