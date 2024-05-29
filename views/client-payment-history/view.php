<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistory */

$this->title = $model->client_payment_history_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Payment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-payment-history-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'client_payment_history_id',
            'user.username',
            'customer.client_name',
            'received_type',
            'paymentType.payment_type_name',
            'received_amount',
            'remarks',
            'status',
            'received_at',
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'customerPaymentHistoryGrid',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->client_payment_history_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

</div>
