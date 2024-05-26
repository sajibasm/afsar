<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockOutlet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-stock-outlet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_stock_outlet_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList([ 'Received' => 'Received', 'Transfer' => 'Transfer', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'params')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'transferOutlet')->textInput() ?>

    <?= $form->field($model, 'receivedOutlet')->textInput() ?>

    <?= $form->field($model, 'transferBy')->textInput() ?>

    <?= $form->field($model, 'transferApprovedBy')->textInput() ?>

    <?= $form->field($model, 'receivedBy')->textInput() ?>

    <?= $form->field($model, 'receivedApprovedBy')->textInput() ?>

    <?= $form->field($model, 'createdAt')->textInput() ?>

    <?= $form->field($model, 'updatedAt')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending', 'reject' => 'Reject', ], ['prompt' => '']) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
