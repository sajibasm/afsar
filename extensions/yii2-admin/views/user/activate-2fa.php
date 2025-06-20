<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Verify 2FA';
?>

<div class="box box-primary">
    <div class="box-header with-border">
    </div>
    <div class="box-body" id="activate-2fa">

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

        <div class="verify-2fa-container">
            <h2>Verify Two-Factor Authentication</h2>
            <p>Scan the QR code using Google Authenticator or similar app</p>

            <div class="qr-image">
                <img src="<?= $qrCodeUrl ?>" alt="QR Code">
            </div>

            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'otp_input')->textInput([
                'placeholder' => 'Enter the 6-digit code',
                'class' => 'form-control'
            ])->label(false) ?>

            <?= Html::submitButton('Verify & Enable 2FA', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>

    </div>
</div>