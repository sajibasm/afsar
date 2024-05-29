<?php

use app\components\ProductUtility;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BrandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?php
            //echo Html::activeHiddenInput($model, 'totalQuantity');
            echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ProductUtility::getItemList('active',  'item_name ASC'), 'item_id', 'item_name'),
                'options' => [
                    'id' => 'item_id',
                    'placeholder' => 'Select items'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'brand_name') ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'brand_status')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' =>['Active'=>'Active','Inactive'=>'Inactive'],
                'options' => [
                    'id' => 'expense_type_status_iud',
                    'placeholder' => 'Status'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
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
