<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStatementOutlet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-statement-outlet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'outlet_id')->textInput() ?>

    <?= $form->field($model, 'item_id')->textInput() ?>

    <?= $form->field($model, 'brand_id')->textInput() ?>

    <?= $form->field($model, 'size_id')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'Sales' => 'Sales', 'Sales-Update' => 'Sales-Update', 'Sales-Return' => 'Sales-Return', 'Stock' => 'Stock', 'Stock-Return' => 'Stock-Return', 'Market-Sales' => 'Market-Sales', 'Market-Sales-Update' => 'Market-Sales-Update', 'Market-Return' => 'Market-Return', 'Stock-Received' => 'Stock-Received', 'Stock-Transfer' => 'Stock-Transfer', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reference_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
