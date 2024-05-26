<?php

use app\components\ProductUtility;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\SalesDraft */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .alert {
        border: 1px solid transparent;
        border-radius: 4px;
        margin-bottom: 5px;
        padding: 6px;
        text-align: center;
        /*height: 36px;*/
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
            'id'=>'salesUpdateProduct',
        ]); ?>

        <div class="alert alert-success" role="alert" id="success-message" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="alert alert-danger" role="alert" id="danger-message" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>


        <div class="row">
            <div class="col-sm-4">
                <?php
                    echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => ArrayHelper::map(ProductUtility::getItemList(), 'item_id', 'item_name'),
                        'options' => [
                            'id'=>'item_id',
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
                    //'theme'=>Select2::THEME_DEFAULT,
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT],
                    'options' => ['id'=>'brand_id'],
                    'pluginOptions'=>[
                        'depends'=>['item_id'],
                        'placeholder' => 'Select brand',
                        'url' => Url::to(['/sales/get-brand-list-by-item'])
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-4">
                <?php
                echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                    //'theme'=>Select2::THEME_DEFAULT,
                    'type'=>DepDrop::TYPE_SELECT2,
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT],
                    'options' => ['id'=>'size_id'],
                    'pluginOptions'=>[
                        'depends'=>['item_id','brand_id'],
                        'placeholder' => 'Select size',
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
                        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                        'options' => ['id'=>'sales_amount'],
                        'pluginOptions'=>[
                            'depends'=>['size_id'],
                            'placeholder' => 'Select Rate ... ',
                            'url' => Url::to(['/sales/get-product-price'])
                        ]
                    ]);
                ?>
            </div>

            <div class="col-sm-4">
                <?= Html::activeHiddenInput($model , 'cost_amount')?>
                <?= Html::activeHiddenInput($model, 'type'); ?>
                <?= Html::activeHiddenInput($model, 'lowestPercent'); ?>
                <?= $form->field($model, 'price')->textInput(['placeholder'=>'Unit Price Per Qty', 'readOnly'=>true]) ?>
            </div>

            <div class="col-sm-4">

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'quantity')->textInput(['placeholder'=>'Quantity']) ?>
                    </div>
                    <div class="col-md-6">
                        <label for="salesdraft-challan_unit" class="control-label" style="padding-top: 15px;"></label>
                        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success btn-block btn-flat']) ?>
                    </div>
                </div>

            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>









