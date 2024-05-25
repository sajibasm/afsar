<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductItemsPrice */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product Price',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Items Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-items-price-update">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Product Price</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="size-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
