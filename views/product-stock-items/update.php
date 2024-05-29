<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItems */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product Stock Items',
]) . ' ' . $model->product_stock_items_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Stock Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_stock_items_id, 'url' => ['view', 'id' => $model->product_stock_items_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-stock-items-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
