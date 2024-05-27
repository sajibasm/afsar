<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\OutletUtility;
use kartik\daterange\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-payment-history-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
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
            <?= $form->field($model, 'client_payment_history_id') ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'received_type')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => CommonUtility::getCustomerPaymentReceivedType(),
                'options' => [
                    'placeholder' => 'Select Received Type'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'payment_type_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getPaymentType(false, 'active'), 'payment_type_id', 'payment_type_name'),
                'options' => [
                    'placeholder' => 'Select a type'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>


        <div class="col-md-6">
            <?= $form->field($model, 'received_amount') ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo '<label class="control-label">Date Range</label>';
            echo DateRangePicker::widget([
                'model'=>$model,
                'attribute'=>'received_at',
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
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
