<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStock */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product Stock',
]) . ' ' . $model->product_stock_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Stocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_stock_id, 'url' => ['view', 'id' => $model->product_stock_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-stock-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
