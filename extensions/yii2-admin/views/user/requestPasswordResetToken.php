<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \mdm\admin\models\form\PasswordResetRequest */

$this->title = 'Request password reset';
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
        <div class="login-box-modern">
            <h3 class="login-title">Forgot Password?</h3>

            <p>Please enter your email. A reset link will be sent to your inbox.</p>

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

            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput([
                'placeholder' => 'Email address',
                'class' => 'form-control login-input'
            ])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Send Reset Link', ['class' => 'btn btn-login btn-block']) ?>
            </div>

            <div class="text-right" style="margin-bottom: 10px;">
                <?= Html::a('← Back to Login', ['/admin/user/login'], ['class' => 'back-to-login-link']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
