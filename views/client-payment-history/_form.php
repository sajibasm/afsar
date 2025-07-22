<?php

use app\assets\CustomerPaymentAssets;
use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\StoreUtility;
use app\models\Client;
use app\models\ClientPaymentHistory;
use app\models\PaymentType;
use kartik\number\NumberControl;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistory */
/* @var $form yii\widgets\ActiveForm */

// 1. Default payment type
$defaultPaymentType = isset($model->paymentType->payment_type_name)
    ? $model->paymentType->payment_type_name
    : PaymentType::TYPE_CASH;

$this->registerJsVar('defaultPaymentType', $defaultPaymentType);

// 2. Bank type (constant)
$this->registerJsVar('bankType', PaymentType::TYPE_DEPOSIT);

// 3. Client due (URL base)
$this->registerJsVar('clientDue', Url::base(true));

// 4. Payment type map
$paymentTypes = [];
foreach (CommonUtility::getPaymentTypeList() as $type) {
    $paymentTypes[$type->payment_type_id] = $type->type;
}
$this->registerJsVar('type', $paymentTypes);
$asset = CustomerPaymentAssets::register($this);
$this->registerJsFile($asset->baseUrl . '/create-payment-history.js', ['depends' => CustomerPaymentAssets::class]);

?>

<div class="client-payment-history-form">


    <div class="alert alert-success" id="totalDues" style="display: none;"></div>

    <?php $form = ActiveForm::begin([
        'id' => 'formPaymentReceived'
    ]) ?>
    <div class="row">

        <div class="col-md-6">
            <?php

            if (StoreUtility::countUserStores() > 1) {
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => StoreUtility::getUserStores(),
                    'options' => [
                        //'id' => 'outlet_id',
                        'placeholder' => 'Store'
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
                        'placeholder' => 'Store '
                    ]
                ]);
            }
            ?>
        </div>
        <div class="col-md-6">
            <?php
            if (StoreUtility::countUserStores() > 1) {
                echo $form->field($model, 'client_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'data' => !empty($model->outletId) ? CustomerUtility::findCustomersWithAddresses(null, 'client_name asc', true, $model->outletId) : [],
                    'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                    //'options' => ['value'=>$model->client_id]
                    'pluginOptions' => [
                        'depends' => ['clientpaymenthistory-outletid'],
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
                    ]
                ]);
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'source')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ClientPaymentHistory::getFormReceivedType(),
                'options' => [
                    'placeholder' => 'Select one'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'payment_type_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getPaymentTypeList(), 'payment_type_id', 'payment_type_name'),
                'options' => [
                    'placeholder' => 'Select a type'
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
            echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getAllBank(), 'bank_id', 'bank_name'),
                'options' => [
                    'id' => 'bank_id',
                    'placeholder' => 'Select a bank'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $model->isNewRecord ? [] : ArrayHelper::map(CommonUtility::getBranchListByBankId($model->bank_id), 'branch_id', 'branch_name'),
                'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                'options' => ['id' => 'branch_id'],
                'pluginOptions' => [
                    'depends' => ['bank_id'],
                    'placeholder' => 'Select a branch',
                    'url' => Url::to(['/bank/get-branch'])
                ]
            ]);
            ?>
        </div>

    </div>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'received_amount')->textInput([]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'remarks')->textInput([]) ?>
        </div>
    </div>


    <div class="panel-footer">
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center">
                    <?= \app\components\ButtonHelper::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
