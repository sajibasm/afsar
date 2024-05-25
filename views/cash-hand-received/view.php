<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CashHandReceived */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Hand Receiveds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-hand-received-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user.username',
            'received_amount',
            'remarks',
            'created_at',
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'cashHandReceivedAjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

</div>
