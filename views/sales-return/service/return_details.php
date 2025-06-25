<?php

use app\components\CommonUtility;
use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesReturnDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="return-draft-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,

        'layout' => '{items}{pager}',
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'font-weight:bold;'],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'item_id',
                'value'=>function($model){
                    return $model->item->item_name;
                },
            ],

            [
                'attribute'=>'brand_id',
                'value'=>function($model){
                    return $model->brand->brand_name;
                },
            ],

            [
                'attribute'=>'size_id',
                'value'=>function($model){
                    return $model->size->size_name;
                },
            ],
            [
                'attribute'=>'quantity',
            ],
            [
                'header'=>'Unit Price',
                'attribute'=>'refund_amount',
                'footer'=>'Total',
                'value'=>function($model){
                    return $model->refund_amount;
                },
            ],
            [
                'attribute' => 'total_amount',
                'footer' => CommonUtility::pageTotal($dataProvider->models, 'total_amount'),
            ],
        ],
    ]); ?>

</div>
