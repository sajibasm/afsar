<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \mdm\admin\models\form\ResetPassword */

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/login-custom.css');
?>
<?php
$this->registerJs("
    $('#resetpassword-password').on('input', function () {
        const val = $(this).val();
        let strength = 'Weak';
        if (val.length >= 8 && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[^a-zA-Z0-9]/.test(val)) {
            strength = 'Strong';
        } else if (val.length >= 6 && /[A-Z]/.test(val) && /[0-9]/.test(val)) {
            strength = 'Medium';
        }
        $('#password-strength').text('Strength: ' + strength);
        $('#password-strength').css('color',
            strength === 'Strong' ? 'green' :
            strength === 'Medium' ? 'orange' : 'red'
        );
    });

    $('#resetpassword-password').focus(); // Auto-focus
");
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

            <h3 class="login-title">Set a New Password</h3>

            <p>Please enter your new password below.</p>

            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => 'New password',
                'class' => 'form-control login-input'
            ])->label(false) ?>

            <div id="password-strength" style="margin-top: -15px; margin-bottom: 10px; font-size: 13px;"></div>

            <?= $form->field($model, 'retypePassword')->passwordInput([
                'placeholder' => 'Repeat password',
                'class' => 'form-control login-input'
            ])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Save Password', ['class' => 'btn btn-login btn-block']) ?>
            </div>

            <div class="text-right" style="margin-bottom: 10px;">
                <?= Html::a('← Back to Login', ['/admin/user/login'], ['class' => 'back-to-login-link']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
