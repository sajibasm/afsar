<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\WarehousePayment */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouse Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="warehouse-payment-create">

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Warehouse Payment</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="warehouse-payment">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>


</div>
