<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\OutletUtility;
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

if (isset($model->paymentType->payment_type_name)) {
    $var = "var defaultPaymentType='" . $model->paymentType->payment_type_name . "';";
} else {
    $var = "var defaultPaymentType='" . PaymentType::TYPE_CASH . "';";
}

$var .= "bankType='" . PaymentType::TYPE_DEPOSIT . "'; var clientDue = '" . Url::base(true) . "';";
$var .= 'var type = {';
foreach (CommonUtility::getPaymentType() as $type) {
    $var = $var . " " . $type->payment_type_id . ": '" . $type->type . "', ";
}
$var = rtrim($var, ', ');
$var = $var . ' };';

$this->registerJs($var, View::POS_HEAD, 'paymentType');
$this->registerJsFile('@web/lib/js/client-payment-history.js', ['depends' => JqueryAsset::className()]);
?>

<div class="client-payment-history-form">


    <div class="alert alert-success" id="totalDues" style="display: none;"></div>

    <?php $form = ActiveForm::begin([
        'id' => 'formPaymentReceived'
    ]) ?>
    <div class="row">

        <div class="col-md-6">
            <?php

            if (OutletUtility::numberOfOutletByUser() > 1) {
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => OutletUtility::getUserOutlet(),
                    'options' => [
                        //'id' => 'outlet_id',
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
                    'type' => DepDrop::TYPE_SELECT2,
                    'data' => !empty($model->outletId) ? CustomerUtility::getCustomerWithAddressList(null, 'client_name asc', true, $model->outletId) : [],
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
                    'data' => CustomerUtility::getCustomerWithAddressList(null, 'client_name asc', true, $model->outletId),
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
                'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
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
                'data' => ArrayHelper::map(CommonUtility::getBank(), 'bank_id', 'bank_name'),
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
                'data' => $model->isNewRecord ? [] : ArrayHelper::map(CommonUtility::getBranchByBankId($model->bank_id), 'branch_id', 'branch_name'),
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
            <?php
            echo $form->field($model, 'received_amount')->widget(NumberControl::className(), [
                'model' => $model,
                'name' => 'normal-decimal',
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'remarks')->textInput([]) ?>
        </div>
    </div>


    <div class="panel-footer">
        <div class="modal-footer">
            <?= Html::submitButton(Yii::t('app', 'Received Payment'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
