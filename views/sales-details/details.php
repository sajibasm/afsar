<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="sales-details-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'',
        'showHeader'=>true,
        'showFooter'=>false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //
            //'sales_id',
            [
                'header'=>'Item',
                'value'=>function($model){
                    return $model->item->item_name;
                }
            ],
            [
                'header'=>'Brand',
                'value'=>function($model){
                    return $model->brand->brand_name;
                }
            ],

            [
                'header'=>'Size',
                'value'=>function($model){
                    return $model->size->size_name;
                }
            ],

            [
                'header'=>'Unit Price',
                'value'=>function($model){
                    return $model->sales_amount;
                }
            ],
            [
                'header'=>'Quantity',
                'value'=>function($model){
                    return $model->quantity;
                }
            ],
            [
                'header'=>'Total Qty',
                'value'=>function($model){
                    return $model->total_amount;
                }
            ],
            //'unit',
            //'cost_amount',
//            'sales_amount',
//            'quantity',
//            'total_amount',
            //'challan_unit',
            //'challan_quantity',
        ],
    ]); ?>

</div>
