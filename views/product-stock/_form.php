<?php

    use app\components\CommonUtility;
    use kartik\widgets\Select2;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-stock-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        echo $form->field($model, 'warehouse_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(CommonUtility::getWarehouseList(), 'warehouse_id', 'warehouse_name'),
            'options' => ['placeholder' => 'Select a Warehosue'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <?php
        echo $form->field($model, 'lc_id')->widget(Select2::classname(), [
            'data' => CommonUtility::getLcNameAndNumberArrayList(),
            'options' => ['placeholder' => 'Select a LC'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::Button(Yii::t('app', 'Close'), ['class'=>'btn btn-default', 'aria-hidden'=>true, 'data-dismiss'=>'modal'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
