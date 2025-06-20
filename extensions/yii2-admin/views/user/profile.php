<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Profile';

?>

<div class="box box-primary">
    <div class="box-header with-border">
    </div>
    <div class="box-body" id="user-index">

<div class="user-profile-update">
    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <?php
    $isEmailEditable = empty($model->email);
    ?>

    <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'first_name')->textInput(['readonly' => false]) ?>
    <?= $form->field($model, 'last_name')->textInput(['readonly' => false]) ?>
    <?= $form->field($model, 'email')->textInput([
        'readonly' => !$isEmailEditable,
        'placeholder' => $isEmailEditable ? 'Enter your email' : null,
    ]) ?>
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter new password']) ?>
    <?= $form->field($model, 'confirm_password')->passwordInput(['placeholder' => 'Confirm new password']) ?>

    <?= $form->field($model, 'is_2fa_enabled')->checkbox() ?>

    <?php if (!empty($qrCodeUrl)): ?>
        <div class="form-group">
            <label>Scan this QR code with your authenticator app:</label><br>
            <img src="<?= $qrCodeUrl ?>" alt="2FA QR Code">
        </div>

        <?= $form->field($model, 'otp_input')->textInput(['placeholder' => 'Enter the 6-digit code'])->label('Verify 2FA Token') ?>
    <?php endif; ?>


    <div class="form-group">
        <?= Html::submitButton('Update Password', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

    </div>
</div>