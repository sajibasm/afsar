<?php

use app\components\CommonUtility;
use app\components\OutletUtility;
use app\models\PaymentType;
use app\models\SalesSearch;
use app\models\Transport;
use kartik\daterange\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">

        <div class="col-md-6">
            <?= $form->field($model, 'first_name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'last_name') ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'username') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email') ?>
        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
