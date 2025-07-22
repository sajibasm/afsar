<?php

use app\assets\ClientPaymentWithdrawAssets;
use app\components\StoreUtility;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerWithdrawSearch */
/* @var $form yii\widgets\ActiveForm */

$asset = ClientPaymentWithdrawAssets::register($this);
$this->registerJsFile($asset->baseUrl . '/js/search.js', ['depends' => ClientPaymentWithdrawAssets::class]);

?>

<div class="customer-withdraw-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'client_payment_refund',
    ]); ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => StoreUtility::getUserStores(),
                'pluginOptions' => [
                    'disabled' => false
                ],
                'options' => [
                    'placeholder' => 'Store '
                ]
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'remarks') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'payment_history_id') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amount') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo '<label class="control-label">Date Range</label>';
            echo DateRangePicker::widget([
                'model'=>$model,
                'attribute'=>'created_at',
                'convertFormat'=>true,
                'includeMonthsFilter'=>true,
                'startAttribute'=>'datetime_start',
                'endAttribute'=>'datetime_end',
                'pluginOptions'=>[
                    'useWithAddon'=>true,
                    'showDropdowns'=>true,
                    'locale'=>[
                        'format'=>'Y-m-d'
                    ]
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= \app\components\ButtonHelper::button(Yii::t('app', 'Search'), [
                    'type' => 'search',
                    'class' => 'btn btn-primary btn-flat'
                ]) ?>

                <?= \app\components\ButtonHelper::button(Yii::t('app', 'Reset'), [
                    'type' => 'reset',
                    'class' => 'btn btn-default btn-flat'
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
