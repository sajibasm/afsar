<?php

use app\components\LcUtility;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LcPaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lc-payment-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'lc_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => LcUtility::getLcList('lc_name', 'lc_name', true),
                'options' => ['placeholder' => 'LC Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'lc_payment_type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => LcUtility::getLcPaymentType('lc_payment_type_name', 'active', true),
                'options' => ['placeholder' => 'LC Head'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'lc_payment_id'); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amount'); ?>
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
