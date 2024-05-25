<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\OutletUtility;
use app\models\Client;

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
/* @var $model app\models\BankReconciliation */
/* @var $form yii\widgets\ActiveForm */

$var = "var defaultType='" . PaymentType::TYPE_DEPOSIT . "';  var type = {";

foreach (CommonUtility::getPaymentType() as $type) {
    $var = $var . " " . $type->payment_type_id . ": '" . $type->type . "', ";
}
$var = rtrim($var, ', ');
$var = $var . ' };';

$this->registerJs($var, View::POS_HEAD, 'bankReconciliation');

$this->registerJsFile(Url::base(true) . '/js/bankReconciliation.js', ['depends' => JqueryAsset::className()]);

?>

<?php $form = ActiveForm::begin() ?>
<div class="bank-reconciliation-form">

    <div class="brand-form">


        <div class="row">

            <div class="col-md-4">

                <?php

                if (OutletUtility::numberOfOutletByUser() > 1) {
                    echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_DEFAULT,
                        'data' => OutletUtility::getUserOutlet(),
                        'options' => [
                            'id' => 'bankreconciliationOutlet',
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
                        'options' => [
                            'id' => 'bankreconciliationOutlet',
                            'placeholder' => 'Outlet '
                        ],
                        'pluginOptions' => [
                            'disabled' => true
                        ]
                    ]);

                }
                ?>
            </div>


            <div class="col-md-4">

                <?php
                if (OutletUtility::numberOfOutletByUser() > 1) {
                    echo $form->field($model, 'customer_id')->widget(DepDrop::classname(), [
                        'type' => DepDrop::TYPE_SELECT2,
                        'data' => !empty($model->outletId) ? CustomerUtility::getCustomerWithAddressList(null, 'client_name asc', true, $model->outletId) : [],
                        'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                        'options' => ['id'=>'bankreconciliation-customer_id'],
                        'pluginOptions' => [
                            'depends' => ['bankreconciliationOutlet'],
                            'placeholder' => 'Select Customer',
                            'url' => Url::to(['/client/by-outlet']),
                            'allowClear' => true
                        ]
                    ]);
                } else {
                    echo $form->field($model, 'customer_id')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_DEFAULT,
                        'data' => CustomerUtility::getCustomerWithAddressList(null, 'client_name asc', true, $model->outletId),
                        'options' => [
                                'id'=>'bankreconciliation-customer_id',
                            'placeholder' => 'Select a customer '
                        ]
                    ]);
                }
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'invoice_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'select2Options' => ['pluginOptions' => ['allowClear' => false], 'theme' => Select2::THEME_DEFAULT,],
                    'options' => ['id' => 'bankreconciliation-invoice_id', 'disabled' => $model->isNewRecord ? false : true],
                    'pluginOptions' => [
                        'depends' => ['bankreconciliation-customer_id'],
                        'placeholder' => 'Select Invoice',
                        'url' => Url::to(['/bank-reconciliation/get-invoice'])
                    ]
                ]); ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-4">  <?php
                echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
                    'options' => [
                        'placeholder' => 'Select a type',

                    ],
                ]);
                ?>
            </div>
            <div class="col-md-4"><?php
                echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getBank(), 'bank_id', 'bank_name'),
                    'options' => [
                        'id' => 'bank_id',
                        'placeholder' => 'Select a bank',
                        'disabled' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>

            <div class="col-md-4">
                <?php
                echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT,],
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
            <div class="col-md-4">
                <?php
                echo $form->field($model, 'reconciliation_type')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CommonUtility::getReconciliationType(), 'id', 'name'),
                    'options' => [
                        'placeholder' => 'Select a type',

                    ],
                ]);
                ?>
            </div>
            <div class="col-md-4">
                <?php
                echo $form->field($model, 'amount')->widget(NumberControl::className(), [
                    'model' => $model,
                    'name' => 'normal-decimal'
                ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

    </div>


    <div class="panel-footer">
        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>



