<?php

use app\components\ProductUtility;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDraft */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-draft-form">

    <?php $form = ActiveForm::begin([
        'id'=>'draftUpdate'
    ]); ?>


    <div class="row">

        <div class="col-sm-4">
            <?php
                echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(ProductUtility::getItemList(), 'item_id', 'item_name'),
                    'options' => [
                        'placeholder' => 'Select a Items',
                        'disabled'=>'disabled'

                    ],
                ]);
            ?>
        </div>

        <div class="col-sm-4">
            <?php
                echo $form->field($model, 'brand_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(ProductUtility::getBrandListByItem($model->item_id), 'brand_id', 'brand_name'),
                    'options' => [
                        'placeholder' => 'Select a Items',
                        'disabled'=>'disabled'

                    ],
                ]);
            ?>
        </div>

        <div class="col-sm-4">
            <?php
                echo $form->field($model, 'size_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(ProductUtility::getSizeListByBrand($model->item_id, $model->brand_id), 'size_id', 'size_name'),
                    'options' => [
                        'placeholder' => 'Select a Items',
                        'disabled'=>'disabled'

                    ],
                ]);
            ?>
        </div>


    </div>

    <div class="row">

        <div class="col-md-4">
            <?= $form->field($model, 'price')->textInput(['readOnly'=>true]) ?>
        </div>


        <div class="col-md-4">
            <?= $form->field($model, 'quantity')->textInput() ?>
        </div>

        <div class="col-md-4 pull-right">
            <div class="form-group field-salesdraft-button">
                <label for="salesdraft-quantity-button" class="control-label"></label>
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-info form-control']) ?>
            </div>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>
