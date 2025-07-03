<?php

use app\components\ProductUtility;
use app\models\Item;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $model app\models\ProductStockOutletSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$this->registerJs(<<<JS
$('#reset-stock-search-form').on('click', function () {
    // Reset the form fields
    const form = $('.product-stock-search form')[0];
    form.reset();

    // Reset Select2
    $('.product-stock-search .select2').each(function() {
        $(this).val(null).trigger('change');
    });

    // Reset DepDrop (child select2s)
    $('#stock_brand_id').html('').trigger('change');
    $('#stock_size_id').html('').trigger('change');

    // Reset DateRangePicker
    $('[name="ProductStockOutletSearch[created_at]"]').val('');
});
JS);
?>


<div class="product-stock-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
    </div>

    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' =>\app\models\ProductStockOutlet::getTypeList(),
                'options' => ['placeholder' => 'Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <?php
            echo '<label class="control-label">Date Range</label>';
            echo DateRangePicker::widget([
                'model'=>$model,
                'attribute'=>'created_at',
                'convertFormat'=>true,
                'includeMonthsFilter'=>true,
                'startAttribute'=>'datetime_start',
                'endAttribute'=>'datetime_end',
                'pluginOptions'=>[
                    'useWithAddon'=>true,
                    'showDropdowns'=>true,
                    'locale'=>[
                        'format'=>'Y-m-d'
                    ]
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12 text-right" style="margin-top: 20px;">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Reset'), [
                    'class' => 'btn btn-default',
                    'id' => 'reset-stock-search-form'
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>



