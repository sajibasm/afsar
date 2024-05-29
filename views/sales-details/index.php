<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales Details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-details-index">
    <p>
        <?= Html::a(Yii::t('app', 'Create Sales Details'), ['create'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'sales_details_id',
            'sales_id',
            'item_id',
            'brand_id',
            'size_id',
            // 'unit',
            // 'cost_amount',
            // 'sales_amount',
            // 'total_amount',
            // 'quantity',
            // 'challan_unit',
            // 'challan_quantity',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'panel' => [
            'type' => 'info',
        ]
    ]); ?>

</div>
