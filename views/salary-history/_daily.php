<?php

use app\components\CommonUtility;
use app\models\PaymentType;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalaryHistory */
/* @var $form yii\widgets\ActiveForm */
$var = "bankType='". PaymentType::TYPE_DEPOSIT."'; ";
$var.= 'var type = {';
foreach(CommonUtility::getPaymentType() as $type){
    $var = $var." ".$type->payment_type_id.": '".$type->type."', ";
}
$var = rtrim($var, ', ');
$var=$var.' };';

$this->registerJs($var, View::POS_HEAD, 'paymentType');

$this->registerJs("var salaryCheck='".Url::base(true).'/'.Yii::$app->controller->id.'/check-salary'."';", View::POS_END, 'checkSalary');
$this->registerJsFile(Url::base(true).'/js/employeeAjax.js', ['depends'=> JqueryAsset::className()]);
?>

<div class="salary-history-form">


    <div class="panel panel-info">

        <div class="panel-heading">
            Advance Salary
        </div>

        <?php $form = ActiveForm::begin() ?>

        <div class="panel-body">
            <div class="salary-history-form">

                <div class="row">
                    <div class="col-md-4">
                        <?php
                        echo $form->field($model, 'employee_id')->widget(Select2::classname(), [
                            'theme'=>Select2::THEME_DEFAULT,
                            'data' => ArrayHelper::map(\app\components\EmployeeUtility::getEmployeeList(), 'id', 'full_name'),
                            'options' => ['placeholder' => 'Select Employee/Stuff'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled'=>!$model->isNewRecord
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?php
                        echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                            'theme'=>Select2::THEME_DEFAULT,
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
                    <div class="col-md-4">
                        <?php
                        echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                            'theme'=>Select2::THEME_DEFAULT,
                            'data' => ArrayHelper::map(CommonUtility::getBank(), 'bank_id', 'bank_name'),
                            'options' => [
                                'id'=>'bank_id',
                                'placeholder' => 'Select a bank'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <?php
                        echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                            'type'=>DepDrop::TYPE_SELECT2,
                            'select2Options'=>['pluginOptions'=>['allowClear'=>true],   'theme'=>Select2::THEME_DEFAULT,],
                            'options' => ['id'=>'branch_id'],
                            'pluginOptions'=>[
                                'depends'=>['bank_id'],
                                'placeholder' => 'Select a branch',
                                'url' => Url::to(['/bank/get-branch'])
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'withdraw_amount')->textInput() ?>
                    </div>

                    <div class="col-md-3">
                        <?= $form->field($model, 'remaining_salary')->textInput(['readOnly'=>true]) ?>
                    </div>

                </div>

            </div>
        </div>


        <div class="panel-footer">

            <div class="modal-footer">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


</div>
