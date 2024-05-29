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

<div class="login-box">
    <div class="login-logo">
        <a href="http://axialsolution.com"><img
                    src="<?php echo Yii::getAlias('@web') . '/images/axial-logo.png' ?>"></a>
    </div>
    <!-- /.login-logo -->

    <div class="login-box-body">
        <p class="login-box-msg">Sign in to your session</p>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <div class="row">
            <div class="col-xs-12">
                <?= $form
                    ->field($model, 'username', $fieldOptions1)
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <?= $form
                    ->field($model, 'password', $fieldOptions2)
                    ->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div id="recaptcha-container"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button', 'id' => 'form-submit-btn']) ?>
            </div>
            <!-- /.col -->
        </div>

        <?php ActiveForm::end(); ?>
        <!-- /.social-auth-links -->
    </div>
</div><!-- /.login-box -->
