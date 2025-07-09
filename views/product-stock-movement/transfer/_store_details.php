<?php

use app\components\StoreUtility;
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
                    echo $form->field($productStock, 'transferOutlet')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => StoreUtility::getStores(),
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'disabled' => true
                        ]
                    ]);
                ?>
            </div>

            <div class="col-md-12">
                <?php
                echo $form->field($productStock, 'receivedOutlet')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => StoreUtility::getAvailableStoresWithStock([$productStock->transferOutlet]),
                    'options' => ['placeholder' => 'To Store'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
                ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($productStock, 'remarks')->textInput(['placeholder' => 'Note']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id' => 'stock-save', 'class' => 'btn btn-primary btn-block']) ?>
            </div>
            <div class="col-md-6">

                <?= Html::a('Cancel',
                    ['/product-stock-movement/discard?type='.ProductStockItemsDraft::TYPE_INSERT.'&source='.ProductStockItemsDraft::SOURCE_MOVEMENT],
                    [
                        'class' => 'btn btn-default btn-block btn-flat',
                        'onclick' => "
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action will cancel the store transfer invoice.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No',
            customClass: {
                popup: 'swal2-confirm-popup',
                title: 'swal2-confirm-title',
                htmlContainer: 'swal2-confirm-text',
                confirmButton: 'swal2-confirm-btn',
                cancelButton: 'swal2-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = $(this).attr('href');
            }
        });
        return false;
    "
                    ]) ?>
            </div>
        </div>



    <?php ActiveForm::end(); ?>
</div>

