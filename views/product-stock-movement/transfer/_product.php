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

    <div class="product-stock-items-draft-form">
        <?php $form = ActiveForm::begin([
            'id'=>'formAjaxStockMovementOutlet',
    ]); ?>

        <div class="alert alert-success" role="alert" id="success-message" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="alert alert-danger" role="alert" id="danger-message" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>


        <div id="errorSummary" class="error-summary" style="display: none"></div>

    <div class="row">

    <div class="col-sm-3">
        <?php
            echo Html::activeHiddenInput($model, 'totalQuantity');
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
            ]);;
        ?>
    </div>
    <div class="col-sm-3">
        <?php
            echo $form->field($model, 'brand_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT],
                'options' => ['id'=>'brand_id'],
                'pluginOptions'=>[
                    'depends'=>['item_id'],
                    'placeholder' => 'Select Brand',
                    'url' => Url::to(['/product-stock/get-brand-list-by-item'])
                ]
            ]);;
        ?>
    </div>
    <div class="col-sm-3">
        <?php
            echo $form->field($model, 'size_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true], 'theme'=>Select2::THEME_DEFAULT],
                'options' => ['id'=>'size_id'],
                'pluginOptions'=>[
                    'depends'=>['item_id','brand_id'],
                    'placeholder' => 'Select Size',
                    'url' => Url::to(['/product-stock/get-size-list-by-brand'])
                ]
            ]);
        ?>
    </div>

    <div class="col-sm-3">

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'new_quantity')->textInput(['placeholder'=>'Quantitiy'])->label('Quantity'); ?>
            </div>
            <div class="col-md-6">
                <label for="stockMovement" class="control-label" style="padding-top: 15px;"></label>
                <?= Html::submitButton(Yii::t('app', 'Add Product'), ['class' => 'btn btn-primary btn-block']) ?>
            </div>
        </div>

    </div>


    </div>

    <?php ActiveForm::end(); ?>

</div>


