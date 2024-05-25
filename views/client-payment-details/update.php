<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentDetails */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Client Payment Details',
]) . ' ' . $model->client_payment_details_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Payment Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->client_payment_details_id, 'url' => ['view', 'id' => $model->client_payment_details_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="client-payment-details-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
