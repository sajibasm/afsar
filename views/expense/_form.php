<?php

use app\components\CommonUtility;
use app\components\ExpenseUtility;
use app\components\OutletUtility;
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
/* @var $model app\models\Expense */
/* @var $form yii\widgets\ActiveForm */

$var = "bankType='" . PaymentType::TYPE_DEPOSIT . "'; ";

$var .= 'var type = {';
foreach (CommonUtility::getPaymentType() as $type) {
    $var = $var . " " . $type->payment_type_id . ": '" . $type->type . "', ";
}
$var = rtrim($var, ', ');
$var = $var . ' };
';
$this->registerJs($var, View::POS_HEAD, 'paymentType');

$this->registerJsFile(Url::base(true) . '/js/expense.js', ['depends' => JqueryAsset::className()]);

?>

<div class="expense-form">


    <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'options' => [
                    'placeholder' => 'Outlet '
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
                echo $form->field($model, 'expense_amount')->widget(NumberControl::className(), [
                    'model' => $model,
                    'name' => 'normal-decimal'
                ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'expense_type_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ExpenseUtility::getExpenseList(), 'expense_type_id', 'expense_type_name'),
                'options' => ['placeholder' => 'Select Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'expense_remarks')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">
            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getPaymentType(), 'payment_type_id', 'payment_type_name'),
                'options' => array(
                    'placeholder' => 'Select a type'
                ),
                'pluginOptions' => array(
                    'allowClear' => true
                ),
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


    <div class="panel-footer">
        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
