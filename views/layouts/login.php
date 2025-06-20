<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';
$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

$this->registerJs("
    var onSubmit = function(token) {
        console.log(token);
        document.getElementById('form-submit-btn').disabled = false;
    };

    var onloadCallback = function() {
        grecaptcha.render('recaptcha-container', {
            'sitekey' : '".getenv('GOOGLE_CAPTCHA_SITE_KEY')."',
            'callback' : onSubmit
        });
    };
", View::POS_END, 'googleCaptcha');

$this->registerCssFile('@web/css/login-custom.css');

?>

<?php
$script = <<<JS
$(document).ready(function() {
    $('#form-submit-btn').prop('disabled', true);
});
JS;
$this->registerJs($script);
?>

<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

<div class="login-page-container">
    <div class="login-left">
        <div class="branding">
            <img src="<?= Yii::getAlias('@web/images/axial-logo.png') ?>" alt="Logo">
            <h1>AXIAL INVENTORY</h1>
            <p>Your Trusted Inventory Solution</p>
        </div>
    </div>

    <div class="login-right">
        <div class="login-box-modern">
            <div class="login-avatar">
                <img src="<?= Yii::getAlias('@web/images/account.png') ?>" alt="Avatar">
            </div>

            <h3 class="login-title">Sign In</h3>

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

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput([
                'placeholder' => 'Username',
                'class' => 'form-control login-input',
            ])->label(false) ?>

            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => 'Password',
                'class' => 'form-control login-input',
            ])->label(false) ?>

            <div class="login-options">
                <?= $form->field($model, 'rememberMe')->checkbox(['template' => "<label class=\"checkbox\">{input} {label}</label>"])->label('Remember me') ?>
                <a href="/admin/user/request-password-reset" class="forgot-link">Forgot Password?</a>
            </div>

            <div id="recaptcha-container" class="recaptcha-wrapper"></div>

            <div class="form-group">
                <?= Html::submitButton('LOGIN', ['class' => 'btn btn-login btn-block', 'id' => 'form-submit-btn']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
