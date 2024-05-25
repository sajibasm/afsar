<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\OutletUtility;
use app\models\Client;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerAccountSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">

        <div class="col-md-6">
            <?php
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
            ])->label('Outlet');
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
            <?php echo $form->field($model, 'fromDate')->widget(
                DateRangePicker::className(),
                [
                    'attributeTo' => 'toDate',
                    'language' => 'en',
                    'size' => 'ms',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-m-dd',
                        'endDate'=>date('Y-m-d'),
                        'todayHighlight'=>true
                    ]
                ]
            ) ?>
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
