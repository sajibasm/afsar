<?php

use app\components\CommonUtility;
use app\components\OutletUtility;
use app\models\PaymentType;
use app\models\SalesSearch;
use app\models\Transport;
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
?>

<div class="sales-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">

        <div class="col-md-6">
            <?php
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
                'data' => ArrayHelper::map(CommonUtility::getPaymentType(false, 'active'), 'payment_type_id', 'payment_type_name'),
                'value' => CommonUtility::getPaymentTypeId(PaymentType::TYPE_CASH),
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

    <?php if(Yii::$app->controller->id=='reports'): ?>
        <div class="row">
            <div class="col-md-12">
                <?php
                echo '<label class="control-label">Date Range</label>';
                echo DateRangePicker::widget([
                    'model'=>$model,
                    'attribute'=>'created_at',
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
    <?php endif;?>

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
