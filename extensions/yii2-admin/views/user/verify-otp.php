<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \mdm\admin\models\form\PasswordResetRequest */

$this->title = 'Verify OTP';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/login-custom.css');
?>
<div class="login-page-container">
    <div class="login-left">
        <div class="branding">
            <img src="<?= Yii::getAlias('@web/images/axial-logo.png') ?>" alt="Logo">
            <h1>AXIAL INVENTORY</h1>
            <p>Your Trusted Inventory Solution</p>
        </div>
    </div>

    <div class="login-right">
        <div class="verify-otp-box-modern">
            <div class="verify-2fa-container">
                <h3>Two-Factor Authentication</h3>
                <p>Please enter the 6-digit code from your Authenticator app.</p>

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'otp')->textInput(['maxlength' => 6, 'class' => 'form-control', 'placeholder' => 'Enter OTP'])->label(false) ?>

                <div class="form-group">
                    <?= Html::submitButton('Verify', ['class' => 'btn btn-primary btn-block']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>
</div>
