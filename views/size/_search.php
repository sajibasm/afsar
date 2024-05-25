<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SizeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="size-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'size_id') ?>

    <?= $form->field($model, 'brand_id') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'size_name') ?>

    <?= $form->field($model, 'size_status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
