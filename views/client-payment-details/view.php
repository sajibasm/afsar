<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentDetails */

$this->title = $model->client_payment_details_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Payment Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-payment-details-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->client_payment_details_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->client_payment_details_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'client_payment_details_id',
            'sales_id',
            'client_id',
            'payment_history_id',
            'client_sales_payment_id',
            'paid_amount',
            'payment_type',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
