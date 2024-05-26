<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BankReconciliation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Reconciliations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-reconciliation-view">

    <div class="row">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'user.username',
                'payment.payment_type_name',
                'reconciliation.name',
                'invoice_id',
                'amount',
                'remarks',
                'created_at',
            ],
        ]) ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'bankReconciliationPjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>



</div>
