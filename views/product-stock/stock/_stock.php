<?php

    use app\components\CommonUtility;
    use app\components\ProductUtility;
use app\models\ProductStock;
use app\models\ProductStockItemsDraft;
use kartik\widgets\DepDrop;
    use kartik\widgets\Select2;
    use yii\bootstrap\Modal;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsDraft */
/* @var $productStock app\models\ProductStock */
/* @var $form yii\widgets\ActiveForm */
?>


    <div class="product-stock-items-draft-form">
        <?php $form = ActiveForm::begin([
            'id'=>'formAjaxSaveStock',
        ]); ?>

        <div class="row">
            <div class="col-md-12">
                <?php
                    echo $form->field($productStock, 'type')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => ['local'=>'Purchase', 'import'=>'Import'],
                        'options' => ['placeholder' => 'Select  Source'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ]);
                ?>
            </div>

            <div class="col-md-12">
                <?php
                echo $form->field($productStock, 'warehouse_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getWarehouseList(), 'warehouse_id', 'warehouse_name'),
                    'options' => ['placeholder' => 'Select  Warehouse'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
                ?>
            </div>

            <div class="col-md-12">
                <?php
                echo $form->field($productStock, 'lc_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => CommonUtility::getLcNameAndNumberArrayList(),
                    'options' => ['placeholder' => 'Select LC'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'dropdownAutoWidth'=>true,
                    ],
                ]);
                ?>
            </div>

            <div class="col-md-12">
                <?php
                echo $form->field($productStock, 'buyer_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getBuyerList(), 'id', 'name'),
                    'options' => ['placeholder' => 'Select  Supplier'],
                    'pluginOptions' => [
                        'allowClear' => true,

                    ],
                ]);
                ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($productStock, 'remarks')->textInput(['placeholder'=>'Note']) ?>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['id'=>'stock-save', 'class' => $model->isNewRecord ? 'btn btn-primary btn-block' : 'btn btn-primary']) ?>
            </div>

            <div class="col-md-6">
                <?= Html::a('Cancel',
                    ['/product-stock/discard?type='. ProductStockItemsDraft::TYPE_UPDATE.'&source='.ProductStockItemsDraft::SOURCE_STOCK],
                    [
                    'class' => 'btn btn-default btn-block btn-flat',
                    'onclick' => "
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action will cancel the stock update.',
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

