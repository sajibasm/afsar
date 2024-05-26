<?php

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\DateTimeUtility;
use app\models\CashBook;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashBookSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-book-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'source')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getCashBookSource(), 'source', 'source'),
                'options' => ['placeholder' => 'Select Source'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'typeFilter')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => CashBook::getTypeFilterList(),
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
            <?= $form->field($model, 'amountFrom') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'amountTo') ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'reference_id') ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => \app\components\OutletUtility::getUserOutlet(),
                'pluginOptions' => [
                    'disabled' => false
                ],
                'options' => [
                    'placeholder' => 'Outlet '
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <?php echo $form->field($model, 'created_at')->widget(
                DateRangePicker::className(),
                [
                    'attributeTo' => 'created_to',
                    'language' => 'en',
                    'size' => 'sm',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => SystemSettings::calenderDateFormat(),
                        'todayHighlight'=>true,
                        'endDate' => DateTimeUtility::getDate(null, SystemSettings::calenderEndDateFormat())
                    ]
                ]
            ) ?>
        </div>
    </div>

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
