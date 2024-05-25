<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Stock Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-stock-items-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'item.item_name',
            'brand.brand_name',
            'size.size_name',
             'cost_price',
             'wholesale_price',
             'retail_price',
             'previous_quantity',
             'new_quantity',
             'total_quantity',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
