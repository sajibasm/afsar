<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProductStockItems */

$this->title = Yii::t('app', 'Create Product Stock Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Stock Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-stock-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
