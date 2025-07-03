<?php
use app\components\SupplierUtility;
use app\components\WarehouseUtility;
use app\models\ProductStock;
use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-stock-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'product_stock_id') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'invoice_no') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'type')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' =>ProductStock::getTypeList(),
                'options' => ['placeholder' => 'Type'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'status')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ProductStock::getStatusList(),
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
            <?php
            echo $form->field($model, 'supplier')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => SupplierUtility::getSupplierList('name', true),
                'options' => ['placeholder' => 'Supplier'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
             echo $form->field($model, 'warehouse')->widget(Select2::classname(), [
                 'theme'=>Select2::THEME_DEFAULT,
                'data' => WarehouseUtility::getWarehouseList('warehouse_name', true),
                'options' => ['placeholder' => 'Warehouse'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'user_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => \yii\helpers\ArrayHelper::map(
                    User::getAllUsers(), // or $model->userList() if you're calling from $model
                    'user_id', // or 'id_user' if that’s your column
                    function ($user) {
                        return ucfirst($user->username);
                    }
                ),
                'options' => ['placeholder' => 'Select User'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
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

    <div class="row">
        <div class="col-md-12 text-right" style="margin-top: 20px;">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>



