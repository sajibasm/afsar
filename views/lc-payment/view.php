<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\LcPayment */

$this->title = $model->lc_payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lc Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lc-payment-view">

    <div class="row">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'lc_payment_id',
                'lc.lc_name',
                'lcPaymentType.lc_payment_type_name',
                'paymentType.payment_type_name',
                'user.username',
                'amount',
                'remarks',
                'status',
                'created_at',
            ],
        ]) ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'LCPaymentpjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->lc_payment_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>
</div>
