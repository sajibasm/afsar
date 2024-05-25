<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsOutlet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-stock-items-outlet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_stock_outlet_id')->textInput() ?>

    <?= $form->field($model, 'item_id')->textInput() ?>

    <?= $form->field($model, 'brand_id')->textInput() ?>

    <?= $form->field($model, 'size_id')->textInput() ?>

    <?= $form->field($model, 'cost_price')->textInput() ?>

    <?= $form->field($model, 'wholesale_price')->textInput() ?>

    <?= $form->field($model, 'retail_price')->textInput() ?>

    <?= $form->field($model, 'previous_quantity')->textInput() ?>

    <?= $form->field($model, 'new_quantity')->textInput() ?>

    <?= $form->field($model, 'total_quantity')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'pending' => 'Pending', 'done' => 'Done', ], ['prompt' => '']) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
