<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = $model->sales_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-view">



    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sales_id',
            'client_name',
            'user.username',
            'contact_number',
            'paid_amount',
            'due_amount',
            'discount_amount',
            'total_amount',
            'remarks',
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'salesPjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->sales_id)])]) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-warning', 'id'=>'cancelConfirmation', 'data-view'=>'salesPjaxGridView',  'data-link'=>Url::to(['restore','id'=>Utility::encrypt($model->sales_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

</div>
