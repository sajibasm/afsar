<?php

use app\components\ProductUtility;
use app\models\Item;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStatementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-statement-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>



    <div class="row">
        <div class="col-sm-6">
            <?php
            echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ProductUtility::getItemList(Item::STATUS_ACTIVE, 'item_name', true),
                'options' => [
                    'id'=>'stock_item_id',
                    'placeholder' => 'Select a items'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Item');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'brand_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'stock_brand_id'],
                'pluginOptions'=>[
                    'depends'=>['stock_item_id'],
                    'placeholder' => 'Select a brand',
                    'url' => Url::to(['/product-stock/get-brand-list-by-item'])
                ]
            ])->label('Brand');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true],  'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'stock_size_id'],
                'pluginOptions'=>[
                    'depends'=>['stock_item_id','stock_brand_id'],
                    'placeholder' => 'Select a size',
                    'url' => Url::to(['/product-stock/get-size-list-by-brand'])
                ]
            ])->label('Size')
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php echo $form->field($model, 'reference_id') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'quantity') ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?php
//            echo $form->field($model, 'type')->widget(Select2::classname(), [
//                'data' => ArrayHelper::map(ProductUtility::getProductStatementType(), 'type', 'type'),
//                'options' => [
//                    'id'=>'type',
//                    'placeholder' => 'Select Type'
//                ],
//                'pluginOptions' => [
//                    'allowClear' => true
//                ],
//            ]);
            ?>
        </div>
        <div class="col-md-6">
<!--            --><?php //echo $form->field($model, 'created_at')->widget(
//                DateRangePicker::className(),
//                [
//                    'attributeTo' => 'created_to',
//                    'language' => 'en',
//                    'size' => 'sm',
//                    'clientOptions' => [
//                        'autoclose' => true,
//                        'format' => 'yyyy-m-dd'
//                    ]
//                ]
//            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
