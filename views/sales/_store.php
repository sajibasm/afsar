<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\StoreUtility;
use app\components\Utility;
use app\models\PaymentType;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Sales Store';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
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
                <?= $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => StoreUtility::getUserStores(),
                    'options' => ['placeholder' => 'Select Your Store']
                ])->label('Store') ?>
            </div>

            <div class="col-md-6">
                <div class="form-group text-left" style="margin-top: 25px;">
                    <?= Html::submitButton('Go to Sales', ['class' => 'btn btn-primary', 'style' => 'margin-right:10px;']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
