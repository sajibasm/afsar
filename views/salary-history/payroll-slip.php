<?php

use app\components\EmployeeUtility;
use app\models\Employee;
use kartik\checkbox\CheckboxX;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

use yii\web\View;
use yii\widgets\Pjax;

//$this->registerJsFile(Url::base(true).'/js/sales.js', ['depends'=> JqueryAsset::className()]);
/* @var $this yii\web\View */
/* @var $model app\models\SalaryHistory */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Payroll Slip');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payroll'), 'url' => ['index']];

$this->registerJsFile(Url::base(true).'/js/employeeAjax.js', ['depends'=> JqueryAsset::className()]);

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Payroll Slip</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body" id="sales_product_details">
        <?php $form = ActiveForm::begin() ?>
        <div class="transport-form">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo $form->field($model, 'employee_id')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => EmployeeUtility::getEmployeeList('full_name', Employee::ACTIVE_STATUS, true),
                        'maintainOrder' => true,
                        'options' => [
                            'placeholder' => 'Select a employee',
                            'multiple' => true,
                            'id'=>'slipEmployee'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            //'minimumInputLength' => 2,
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                    echo $form->field($model, 'month')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'showToggleAll' => false,
                        'data' => \app\components\CommonUtility::getMonth(),
                        'maintainOrder' => true,
                        'options' => [
                            'placeholder' => 'Select a month',
                            'id'=>'slipMonth'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                    echo $form->field($model, 'year')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => \app\components\CommonUtility::getYear(),
                        'maintainOrder' => true,
                        'options' => [
                            'placeholder' => 'Select a year',
                             'id'=>'slipYear'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <div class="modal-footer">
                <?= Html::button(Yii::t('app', 'Send'), ['class'=>'btn btn-info', 'id'=>'payrollSlip']) ?>
                <?= Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
