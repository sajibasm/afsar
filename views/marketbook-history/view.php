<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MarketBookHistory */

$this->title = $model->market_sales_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Market Book Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="market-book-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->market_sales_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->market_sales_id], [
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
            'market_sales_id',
            'sales_id',
            'client_id',
            'item_id',
            'brand_id',
            'size_id',
            'unit',
            'cost_amount',
            'sales_amount',
            'total_amount',
            'quantity',
            'user_id',
            'remarks',
            'created_at',
            'updated_at',
            'status',
        ],
    ]) ?>

</div>
