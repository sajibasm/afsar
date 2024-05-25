<?php

use app\components\OutletUtility;
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
                'data' => OutletUtility::getOutlet(),
                'options' => ['placeholder' => 'Select  Outlet'],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ])->label('Outlet');
            ?>
        </div>

    <div class="col-md-12">
        <?= $form->field($productStock, 'remarks')->textInput(['placeholder' => '']) ?>
    </div>

    <div class="col-md-12">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Transfer') : Yii::t('app', 'Update'), ['id' => 'stock-save', 'class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>

