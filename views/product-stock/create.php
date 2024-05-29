<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProductStock */

$this->title = Yii::t('app', 'Create Product Stock');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Stocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-stock-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
