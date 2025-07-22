<?php

use app\components\CommonUtility;
use app\components\StoreUtility;
use app\models\PaymentType;
use app\models\Sales;
use app\models\SalesSearch;
use app\models\Transport;
use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs(<<<JS
// On Reset button click
$('#sales-search').on('reset', function() {

    // Reset Select2 dropdowns
    $('#outlet_id').val(null).trigger('change');                             // Store
    $('#salessearch-client_id').val(null).trigger('change');                 // Customer (DepDrop + Select2)
    $('#salessearch-transport_id').val(null).trigger('change');              // Transport
    $('#salessearch-payment_type').val(null).trigger('change');              // Received Type
    $('#salessearch-invoicetype').val(null).trigger('change');               // Type
    $('#salessearch-user_id').val(null).trigger('change');                   // User

    // Reset Date Range Picker
    if ($('#salessearch-created_at').data('daterangepicker')) {
        let picker = $('#salessearch-created_at').data('daterangepicker');
        picker.setStartDate(moment());
        picker.setEndDate(moment());
        $('#salessearch-created_at').val('');
    }

});

JS);
?>

<div class="sales-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
        'id'=>'sales-search',
    ]); ?>


    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => StoreUtility::getUserStores(),
                'options' => [
                    'id' => 'outlet_id',
                    'placeholder' => 'Store'
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
            <?= $form->field($model, 'contact_number') ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'transport_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(Transport::find()->all(), 'transport_id', 'transport_name'),
                'options' => [
                    'placeholder' => 'Select Transport '
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
            <?= $form->field($model, 'tracking_number') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sales_id') ?>
        </div>
    </div>


    <div class="row">

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getPaymentTypeList(false, 'active'), 'payment_type_id', 'payment_type_name'),
                'value' => CommonUtility::getDefaultPaymentTypeId(PaymentType::TYPE_CASH),
                'options' => [
                    'placeholder' => 'Select...',

                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Received Type');
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'invoiceType')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => [SalesSearch::PAYMENT_PARTIAL=>SalesSearch::PAYMENT_PARTIAL, SalesSearch::PAYMENT_PAID=>SalesSearch::PAYMENT_PAID, SalesSearch::PAYMENT_CREDIT=>SalesSearch::PAYMENT_CREDIT],
                //'value' =>,
                'options' => [
                    'placeholder' => 'Select...',

                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Type');
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'payment_condition_search')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => Sales::getPaymentConditionOptions(),
                'options' => [
                    'placeholder' => 'Select Payment Condition...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Payment Condition');
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'due_date_range')->widget(DateRangePicker::class, [
                'convertFormat' => true,
                'startAttribute' => 'due_date_start',
                'endAttribute' => 'due_date_end',
                'pluginOptions' => [
                    'useWithAddon' => true,
                    'showDropdowns' => true,
                    'locale' => [
                        'format' => 'Y-m-d', // Correct Moment.js format
                        'separator' => ' to ',     // Optional: adds a clear separator
                    ],
                    'autoclose' => true,
                    'todayHighlight' => true,
                ],
                'options' => [
                    'placeholder' => 'Select Due Date Range...',
                ],
            ])->label('Payment Due Date');
            ?>
        </div>
    </div>

        <div class="row">
            <div class="col-md-6">
                <?php
                echo $form->field($model, 'user_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => \yii\helpers\ArrayHelper::map(
                        User::getAllUsers(), // or $model->userList() if you're calling from $model
                        'user_id', // or 'id_user' if that’s your column
                        function ($user) {
                            return ucfirst($user->username);
                        }
                    ),
                    'options' => ['placeholder' => 'Select User'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <label class="control-label">Date Range</label>
                <?php
                $today = date('Y-m-d');
                echo DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'created_at',
                    'convertFormat' => true,
                    'includeMonthsFilter' => true,
                    'startAttribute' => 'datetime_start',
                    'endAttribute' => 'datetime_end',
                    'disabled' => (Yii::$app->controller->id == 'reports') ? false : true,
                    'pluginOptions' => [
                        'useWithAddon' => true,
                        'showDropdowns' => true,
                        'opens' => 'left',
                        'maxDate' => $today,
                        'locale' => [
                            'format' => 'Y-m-d',
                        ]
                    ],
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
