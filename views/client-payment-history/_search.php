<?php

use app\components\ButtonHelper;
use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\StoreUtility;
use kartik\daterange\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistorySearch */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs(<<<JS
$('#clientpaymenthistorysearch').on('reset', function(e) {
    // Timeout ensures the native reset happens before triggering JS resets
    setTimeout(function () {
        // Reset Select2 fields
        $('#outlet_id').val(null).trigger('change'); // Outlet
        $('#clientpaymenthistorysearch-client_id').val(null).trigger('change'); // Customer
        $('#clientpaymenthistorysearch-received_type').val(null).trigger('change'); // Received Type
        $('#clientpaymenthistorysearch-payment_type_id').val(null).trigger('change'); // Payment Type

        // Reset plain input fields
        $('#clientpaymenthistorysearch-client_payment_history_id').val('');
        $('#clientpaymenthistorysearch-received_amount').val('');

        // Reset Date Range Picker
        let dateRangeInput = $('#clientpaymenthistorysearch-received_at');
        if (dateRangeInput.data('daterangepicker')) {
            let picker = dateRangeInput.data('daterangepicker');
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            dateRangeInput.val('');
        }
    }, 0);
});
JS);
?>

<div class="client-payment-history-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
        'id'=>'clientpaymenthistorysearch',
    ]); ?>

    <div class="row">

        <div class="col-md-6">
            <?php
            if (StoreUtility::countUserStores() > 1) {
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => StoreUtility::getUserStores(),
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
                    'data' => StoreUtility::getUserStores(),
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
            if (StoreUtility::countUserStores() > 1) {
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
                    'data' => CustomerUtility::findCustomersWithAddresses(null, 'client_name asc', true, $model->outletId),
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
                'data' => ArrayHelper::map(CommonUtility::getPaymentTypeList(false, 'active'), 'payment_type_id', 'payment_type_name'),
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
//                'disabled' => (Yii::$app->controller->id == 'reports') ? false : true,
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
                <?= ButtonHelper::button(Yii::t('app', 'Search'), [
                    'type' => 'search',
                    'class' => 'btn btn-primary btn-flat'
                ]) ?>

                <?= ButtonHelper::button(Yii::t('app', 'Reset'), [
                    'type' => 'reset',
                    'class' => 'btn btn-default btn-flat'
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
