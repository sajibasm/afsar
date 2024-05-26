<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductItemsPrice */

$this->title = $model->product_stock_items_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Items Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-items-price-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->product_stock_items_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->product_stock_items_id], [
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
            'product_stock_items_id',
            'item_id',
            'brand_id',
            'size_id',
            'cost_price',
            'wholesale_price',
            'retail_price',
            'quantity',
            'total_quantity',
            'alert_quantity',
        ],
    ]) ?>

</div>
