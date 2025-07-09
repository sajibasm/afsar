<?php


use app\components\StoreUtility;
use aryelds\sweetalert\SweetAlert;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockOutlet */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Store');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Store'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <?php $form = ActiveForm::begin(['id' => 'formAjaxSellCreate']); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'transferOutlet')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => StoreUtility::getUserStores(),
                    'options' => ['placeholder' => 'Select Your Store']
                ])->label('Store') ?>
            </div>

            <div class="col-md-6">
                <div class="form-group text-left" style="margin-top: 25px;">
                    <?= Html::submitButton('Transfer', ['class' => 'btn btn-primary', 'style' => 'margin-right:10px;']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
