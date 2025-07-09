<?php

use app\components\ButtonHelper;
use app\components\StoreUtility;
use app\models\ProductStockItemsDraft;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */
/* @var $productStock app\models\ProductStock */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="product-stock-items-draft-form">
    <?php $form = ActiveForm::begin([
        'id' => 'formAjaxSaveStock',
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($productStock, 'outlet')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => StoreUtility::getStores(),
                'options' => ['placeholder' => 'Select Your Store'],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]);
            ?>
        </div>
    </div>

        <div class="row">
            <div class="col-md-12">
            <?= $form->field($productStock, 'remarks')->textInput(['placeholder' => '']) ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <?= ButtonHelper::button($model->isNewRecord ? 'Transfer': 'Update', ['type' => 'submit']); ?>
            </div>
            <div class="col-md-6">
                <?= ButtonHelper::button('Cancel', [
                    'type' => 'link',
                    'url' =>  ['/product-stock/discard?type='. ProductStockItemsDraft::TYPE_UPDATE.'&source='.ProductStockItemsDraft::SOURCE_MOVEMENT],
                    'confirm'=>true,
                    'confirmText' => 'Are you sure you want to cancel this?',
                    'confirmButton' => 'Yes, cancel it!',
                ]);
                ?>
            </div>
        </div>

<?php ActiveForm::end(); ?>
</div>

