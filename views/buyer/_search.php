<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BuyerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="buyer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'contact_number') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'contact_person') ?>
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
