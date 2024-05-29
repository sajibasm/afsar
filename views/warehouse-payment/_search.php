<?php
use app\components\CommonUtility;
use app\components\WarehouseUtility;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WarehousePaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-payment-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'warehouse_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data'=> WarehouseUtility::getWarehouseList('warehouse_name',  true),
                'options' => ['placeholder' => 'Warehouse'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'month')->widget(Select2::classname(), array(
                'theme'=>Select2::THEME_DEFAULT,
                'data' => CommonUtility::getMonth(),
                'options' => array('placeholder' => 'Select month'),
                'pluginOptions' => array(
                    'allowClear' => true
                ),
            ));
            ?>
        </div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'year')->widget(Select2::classname(), array(
                'theme'=>Select2::THEME_DEFAULT,
                'data' => CommonUtility::getYear(),
                'options' => array('placeholder' => 'Select year'),
                'pluginOptions' => array(
                    'allowClear' => true
                ),
            ));
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'id'); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'payment_amount'); ?>
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
