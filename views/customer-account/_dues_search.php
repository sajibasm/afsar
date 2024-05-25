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
        'action' => ['dues'],
        'method' => 'get',
    ]); ?>

    <div class="row">

        <div class="col-md-6">
            <?php

            if (OutletUtility::numberOfOutletByUser() > 1) {
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
            } else {
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => OutletUtility::getUserOutlet(),
                    'pluginOptions' => [
                        'disabled' => true
                    ],
                    'options' => [
                        'placeholder' => 'Outlet '
                    ]
                ]);

            }
            ?>

        </div>

        <div class="col-md-6">
            <?php
            if (OutletUtility::numberOfOutletByUser() > 1) {
                echo $form->field($model, 'client_id')->widget(DepDrop::classname(), [
                    //'theme'=>Select2::THEME_DEFAULT,
                    'type' => DepDrop::TYPE_SELECT2,
                    'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                    //'options' => ['id' => 'brand_id'],
                    'pluginOptions' => [
                        'depends' => ['outlet_id'],
                        'placeholder' => 'Select Customer',
                        'url' => Url::to(['/client/by-outlet']),
                        'allowClear' => true
                    ]
                ]);
            } else {
                echo $form->field($model, 'client_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => CustomerUtility::getCustomerWithAddressList(null, 'client_name asc', true, $model->outletId),
                    'options' => [
                        'placeholder' => 'Select a customer '
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'duration')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ['days'=>'Days', 'month'=>'Month', 'year'=>'Year'],
                'options' => [
                    'placeholder' => 'select duration'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Duration');
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'durationValues')->textInput()->label('Value') ?>
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
