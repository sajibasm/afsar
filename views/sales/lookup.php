<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Invoice Lookup';
?>

<div class="invoice-lookup-container" style="max-width: 500px; margin: 0 auto; padding: 20px;">
    <h3 class="text-center mb-4"><?= Html::encode($this->title) ?></h3>

    <p class="text-center text-muted">Please enter your Invoice ID to view your invoice as PDF.</p>

    <?php $form = ActiveForm::begin(['method' => 'post']); ?>
    <?= $form->field($inputModel, 'invoice_id')->textInput([
        'placeholder' => 'Enter Invoice ID',
        'class' => 'form-control text-center',
        'autocomplete' => 'off'
    ])->label(false) ?>

    <div class="text-center">
        <?= Html::submitButton('View Invoice', ['class' => 'btn btn-primary btn-block']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <br>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger text-center">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
</div>
