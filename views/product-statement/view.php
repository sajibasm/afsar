<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductStatement */

$this->title = $model->product_statement_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Statements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-statement-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->product_statement_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->product_statement_id], [
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
            'product_statement_id',
            'item_id',
            'brand_id',
            'size_id',
            'quantity',
            'type',
            'remarks',
            'reference_id',
            'user_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
