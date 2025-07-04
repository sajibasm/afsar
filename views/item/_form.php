<?php

use app\components\ProductUtility;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>



<?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'item_name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'brand_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(ProductUtility::getBrandListMap(), 'id', 'name'),
                'options' => ['placeholder' => 'Select a Items'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'product_status')->dropDownList([ 'Active' => 'Active', 'Inactive' => 'Inactive', ]) ?>
        </div>
    </div>

    <div class="panel-footer">
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center">
                    <?= \app\components\ButtonHelper::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>


