<?php

use app\components\SystemSettings;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\UserUtility;
use app\models\Client;
use app\models\User;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\components\OutletUtility;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\SalesReturnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-return-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">

        <div class="col-md-6">
            <?php
            //echo Html::activeHiddenInput($model, 'totalQuantity');
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'options' => [
                    'id' => 'outlet_id',
                    'placeholder' => 'Outlet'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'client_id')->widget(DepDrop::classname(), [
                //'theme'=>Select2::THEME_DEFAULT,
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                //'options' => ['id' => 'brand_id'],
                'pluginOptions' => [
                    'depends' => ['outlet_id'],
                    'placeholder' => 'Select Customer',
                    'url' => Url::to(['/client/by-outlet'])
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'sales_id') ?>
        </div>
        <div class="col-md-6">

            <?= $form->field($model, 'memo_id') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'created_at')->widget(
                DateRangePicker::className(),
                [
                    'attributeTo' => 'created_to',
                    'language' => 'en',
                    'size' => 'ms',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => SystemSettings::calenderDateFormat(),
                        'todayHighlight'=>true,
                        'endDate' => DateTimeUtility::getDate(null, SystemSettings::calenderEndDateFormat())
                    ]
                ]
            )
            ?>
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
