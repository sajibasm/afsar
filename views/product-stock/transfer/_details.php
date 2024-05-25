<?php

use app\components\OutletUtility;
use app\models\ProductStockItemsDraft;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */
/* @var $productStock app\models\ProductStockOutlet */
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
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => OutletUtility::getOutlet(),
                        'options' => ['placeholder' => 'Receive Outlet'],
                        'pluginOptions' => []
                    ])->label('Transfer');
                ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($productStock, 'remarks')->textInput(['placeholder' => 'Note']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id' => 'stock-save', 'class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary']) ?>
            </div>
            <div class="col-md-6">
                <?= Html::a('Discard',['/product-stock/discard?type='.ProductStockItemsDraft::TYPE_INSERT.'&source='.ProductStockItemsDraft::SOURCE_TRANSFER], [
                    'title' => \Yii::t('yii', 'Delete'),
                    'class'=>'btn btn-danger btn-block ',
                    'onclick'=>"
                             if (confirm('do you want to discard( fully reset ) this?')) {
                                return true;
                             }
                       return false;",
                ]);
                ?>
            </div>
        </div>



    <?php ActiveForm::end(); ?>
</div>

