<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistory */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Received Payment',
]) . ' ' . $model->client_payment_history_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Payment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="client-payment-history-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $this->title ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
