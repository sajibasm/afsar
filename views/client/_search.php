<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'client_name') ?>

    <?= $form->field($model, 'client_city') ?>

    <?= $form->field($model, 'client_address1') ?>

    <?= $form->field($model, 'client_address2') ?>

    <?php // echo $form->field($model, 'client_contact_number') ?>

    <?php // echo $form->field($model, 'client_contact_person') ?>

    <?php // echo $form->field($model, 'client_contact_person_number') ?>

    <?php // echo $form->field($model, 'client_type') ?>

    <?php // echo $form->field($model, 'client_balance') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
