<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\models\SmsGateway;

/* @var $this yii\web\View */
/* @var $model app\models\SmsGateway */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-gateway-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'apiKey')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'senderId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'balance')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(SmsGateway::STATUS) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
