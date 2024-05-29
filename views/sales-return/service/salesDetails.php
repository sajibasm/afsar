<?php

use app\components\CommonUtility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="sales-details-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,

        'layout' => '{items}{pager}',
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'font-weight:bold;'],

        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],
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
                'attribute'=>'sales_amount',
                'value'=>function($model){
                    return $model->sales_amount;
                },
                'footer'=>'Total',

            ],
            [
                'header'=>'Sold Qty',
                'value'=>function($model){
                        return $model->quantity;
                },
                //'footer'=> CommonUtility::pageTotal($dataProvider->models,'quantity'). 'Qty',
            ],
            [
                'header'=>'Return Qty',
                'value'=>function($model){
                    if(isset($model->salesReturnDetails->quantity)){
                       return $model->salesReturnDetails->quantity;
                    }else{
                        return 0;
                    }

                }
                //'footer'=> CommonUtility::pageTotal($dataProvider->models->salesReturnDetails,'quantity'). 'Qty',
            ],
            [
                'header'=>'Current Qty',
                'value'=>function($model){

                    if(isset($model->salesReturnDetails->quantity)){
                        return $model->quantity - $model->salesReturnDetails->quantity;
                    }else{
                        return $model->quantity;
                    }

                },
                //'footer'=> CommonUtility::pageTotal($dataProvider->models,'quantity'). 'Qty',
            ],

            [
                'attribute'=>'total_amount',
                'value'=>function($model){

                    if(isset($model->salesReturnDetails->quantity)){
                        return ($model->quantity - $model->salesReturnDetails->quantity)*$model->total_amount;
                    }else{
                        return $model->quantity*$model->total_amount;
                    }
                },
                //'footer'=> CommonUtility::pageTotal($dataProvider->models,'total_amount').' '.Yii::$app->params['currency'],
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
