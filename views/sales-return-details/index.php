<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesReturnDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales Return Details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-return-details-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Sales Return Details'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'sales_details_id',
            'sales_return_id',
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
    ]); ?>

</div>
