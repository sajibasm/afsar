<?php

use app\components\CommonUtility;
use app\components\Utility;
use app\models\SalesReturnDetails;
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
                'class' => 'yii\grid\ActionColumn',
                'header'=>'#',
                'template'=>'{return}',
                'buttons' => [
                    'return' => function ($url, $model) {

                        $total = 0;
                        $items = SalesReturnDetails::getReceivedItemDetailsBySalesAndSize($model->sales_id, $model->size_id);
                        foreach ($items as $item) {
                            $total += $item->quantity;
                        }

                        if($total<$model->quantity){
                            return Html::button('<span class="glyphicon glyphicon-open"></span>', [
                                'class'=>'btn btn-info btn-xs modalUpdateBtn ',
                                'title' => Yii::t('app', $model->item->item_name.' Update'),
                                'id'=>'modalUpdateBtn1',
                                'data-pjax'=>1,
                                'value' =>Url::to(['/sales-return/items','id'=> Utility::encrypt($model->sales_details_id)])
                            ]);
                        }
                    }

                ],
            ],
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
                'header'=>'Unit Price',
                'attribute'=>'sales_amount',
                'value'=>function($model){
                    return $model->sales_amount;
                },
                //'footer'=>'Total',

            ],
            [
                'header'=>'Quantity',
                'value'=>function($model){
                    $total = 0;
                    $items = SalesReturnDetails::getReceivedItemDetailsBySalesAndSize($model->sales_id, $model->size_id);
                    foreach ($items as $item) {
                        $total += $item->quantity;
                    }
                    return $model->quantity - $total;
                },
            ],
            [
                'attribute'=>'total_amount',
                'value'=>function($model){

                    $total = 0;
                    $items = SalesReturnDetails::getReceivedItemDetailsBySalesAndSize($model->sales_id, $model->size_id);
                    foreach ($items as $item) {
                        $total += $item->quantity;
                    }
                    return ($model->quantity - $total)*$model->sales_amount;
                },

            ],
        ],
    ]); ?>

</div>
