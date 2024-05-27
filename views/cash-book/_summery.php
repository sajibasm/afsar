<?php


use app\components\OutletUtility;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;

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
                        'singleDatePicker'=>true,
                        'showDropdowns'=>true,
                        'locale'=>[
                            'format'=>'Y-m-d'
                        ]
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' =>'btn btn-info']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>

