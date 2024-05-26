<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MarketBookHistory */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Market Book History',
]) . $model->market_sales_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Market Book Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->market_sales_id, 'url' => ['view', 'id' => $model->market_sales_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="market-book-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
