<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStatement */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product Statement',
]) . ' ' . $model->product_statement_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Statements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_statement_id, 'url' => ['view', 'id' => $model->product_statement_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-statement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
