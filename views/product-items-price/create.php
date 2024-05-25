<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProductItemsPrice */

$this->title = Yii::t('app', 'Create Product Items Price');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Items Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-items-price-create">
    <div class="box box-success">
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
