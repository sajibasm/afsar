<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\WarehousePayment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Warehouse Payment',
]) . ' ' . $model->warehouse->warehouse_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouse Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="warehouse-payment-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Warehouse Payment Update</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="warehouse-payment">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
