<?php

use app\components\ProductUtility;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MarketBook */
/* @var $form yii\widgets\ActiveForm */
?>


<style>
    .alert {
        border: 1px solid transparent;
        border-radius: 4px;
        margin-bottom: 5px;
        padding: 6px;
        text-align: center;
    }

    .alert-success{
        border-left: 3px solid #3c763d;
    }

    .alert-danger{
        border-left: 3px solid #a87d56;
    }

</style>


    <div class="items-draft-form">

        <?php $form = ActiveForm::begin([
            'id'=>'formAjaxSell',
        ]); ?>

        <div class="alert alert-success" role="alert" id="success-message" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="alert alert-danger" role="alert" id="danger-message" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="alert " role="alert" id="loading" style="display: none">
            <div class="row">
                <div class="col-md-2 col-md-offset-5">
                    <img src="<?= Url::base(true).'/images/loading.gif'?>" alt="">
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-sm-4">
                <?php
                echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(ProductUtility::getItemList(), 'item_id', 'item_name'),
                    'options' => [
                        'id'=>'market_item_id',
                        'placeholder' => 'Select a Items'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-sm-4">
                <?php
                echo $form->field($model, 'brand_id')->widget(DepDrop::classname(), [
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                    'options' => ['id'=>'market_brand_id'],
                    'pluginOptions'=>[
                        'depends'=>['market_item_id'],
                        'placeholder' => 'Select Brand',
                        'url' => Url::to(['/sales/get-brand-list-by-item'])
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-4">
                <?php
                echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                    'options' => ['id'=>'market_size_id'],
                    'pluginOptions'=>[
                        'depends'=>['market_brand_id'],
                        'placeholder' => 'Select Size',
                        'url' => Url::to(['/sales/get-size-list-by-brand']),
                    ]
                ]);
                ?>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-4">
                <?php
                echo $form->field($model, 'sales_amount')->widget(DepDrop::classname(), [
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                    'options' => ['id'=>'market_sales_amount'],
                    'pluginOptions'=>[
                        'depends'=>['market_size_id'],
                        'placeholder' => 'Select Rate ... ',
                        'url' => Url::to(['/sales/get-product-price'])
                    ]
                ]);
                ?>
            </div>

            <div class="col-sm-4">
                <?= $form->field($model, 'price')->textInput(['placeholder'=>'Unit Price', 'readOnly'=>true]) ?>
                <?=  Html::activeHiddenInput($model, 'total_amount');?>
                <?=  Html::activeHiddenInput($model, 'cost_amount');?>
            </div>


            <div class="col-sm-4">
                <?= $form->field($model, 'quantity')->textInput(['placeholder'=>'Quantity']) ?>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-4">
                <?= $form->field($model, 'remarks')->textInput(['placeholder'=>'Remarks']) ?>
                <?= $form->field($model, 'client_id')->hiddenInput() ?>
            </div>

            <div class="col-sm-4">
                <label for="salesdraft-challan_unit" class="control-label"></label>
                <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-primary btn-block']) ?>
            </div>

            <div class="col-sm-4">
                <label for="salesdraft-challan_unit" class="control-label"></label>
                <?= Html::submitButton(Yii::t('app', 'Back'), ['class' => 'btn btn-default btn-block']) ?>
            </div>

        </div>
        <?php ActiveForm::end(); ?>

    </div>









