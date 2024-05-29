<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'item_name') ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'product_status')->widget(Select2::classname(), [
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
