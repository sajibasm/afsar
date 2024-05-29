<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SalesReturn */

$this->title = $model->sales_return_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Returns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-return-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sales_return_id',
            'user.username',
            'sales_id',
            'customer.client_name',
            'client_mobile',
            'refund_amount',
            'cut_off_amount',
            'total_amount',
            'remarks',
            'created_at',
        ],
    ]) ?>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'salesReturn',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->sales_return_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>


</div>
