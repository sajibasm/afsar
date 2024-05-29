<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDetails */

$this->title = $model->sales_details_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-details-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->sales_details_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->sales_details_id], [
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
            'sales_details_id',
            'sales_id',
            'item_id',
            'brand_id',
            'size_id',
            'unit',
            'cost_amount',
            'sales_amount',
            'total_amount',
            'quantity',
            'challan_unit',
            'challan_quantity',
        ],
    ]) ?>

</div>
