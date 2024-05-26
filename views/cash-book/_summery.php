<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\OutletUtility;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashBook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-book-form">

    <?php $form = ActiveForm::begin([
            'method' => 'POST',
            //'action' => Url::to(['reports/cash-report']),
            'options'=>['class'=>'form-inline']]
    ); ?>
        <div class="row">

            <div class="col-md-4">

                <?php
                echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_DEFAULT,
                    'data' => OutletUtility::getUserOutlet(),
                    'pluginOptions' => [
                        'disabled' => false
                    ],
                    'options' => [
                        'placeholder' => 'Outlet '
                    ]
                ]);
                ?>

            </div>

            <div class="col-md-4">
                <?php
                    echo $form->field($model, 'created_at')->widget(DatePicker::className(), [
                            'attribute' => 'created_at',
                            'language' => 'en',
                            'size' => '',
                            'template' => '{addon}{input}',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => SystemSettings::calenderDateFormat(),
                                'todayHighlight'=>true,
                                'endDate' => DateTimeUtility::getDate(null, SystemSettings::calenderEndDateFormat())
                            ]
                        ]
                    )
                ?>
            </div>
            <div class="col-md-4">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' =>'btn btn-info']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>

