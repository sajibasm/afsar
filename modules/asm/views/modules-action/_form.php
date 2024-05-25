<?php

use app\modules\asm\models\Modules;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modules-action-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'module')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Modules::findAll(['active' => 1]), 'id', 'name'),
        'options' => [
            'placeholder' => 'Select a module ...', 'multiple' => false
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Outlet');

    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
