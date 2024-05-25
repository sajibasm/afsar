<?php

use app\models\UserOutlet;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use \app\models\Outlet;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserOutlet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-outlet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'outlet')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Outlet::findAll(['status' => 1, 'type'=>'Outlet']), 'outletId', 'name'),
        'options' => [
                'value'=>ArrayHelper::getColumn(UserOutlet::find()->where(['userId'=>Yii::$app->user->id])->select(['outletId'])->asArray(true)->all(), 'outletId'),
                'placeholder' => 'Select a outlet ...', 'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Outlet');

    ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Assign') : Yii::t('app', 'Assign'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>

</div>
