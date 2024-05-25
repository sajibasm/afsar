<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Stock Movement/Transfer');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'stock_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php
    Utility::gridViewModal($this, $searchModel, '_search_movement');
    Utility::getMessage();
?>



<div class="product-stock-index">


    <?php
        $gridColumns = [
    /*        [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'Serial'
            ],*/
            [
                'header'=>'StockId',
                'attribute' => 'product_stock_id',
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->product_stock_id;
                },
                'group'=>false,  // enable grouping
                //'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                //'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
            ],

            [
                'header'=>'Date',
                'value'=>function($model){
                    return $model->productStock->created_at;
                },
            ],

            [
                'header'=>'Invoice',
                'attribute' => 'invoice_no',
                'hiddenFromExport'=>true,
                'value'=>function($model){
                    return $model->productStock->invoice_no;
                },
            ],

            [
                'header'=>'Type',
                'hiddenFromExport'=>true,
                'attribute' => 'type',
                'value'=>function($model){
                    return $model->productStock->type;
                },
            ],

            [
                'header'=>'Remarks',
                'hiddenFromExport'=>true,
                'value'=>function($model){
                    return $model->productStock->remarks;
                },
            ],
            [
                'attribute' => 'item',
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->item->item_name;
                },
            ],
            [
                'attribute' => 'brand',
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->brand->brand_name;
                }
            ],
            [
                'attribute' => 'size',
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->size->size_name;
                }
            ],
            [
                'header'=>'Cost',
                'attribute' => 'cost_price',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'value'=>function ($model, $key, $index, $widget) {
                    return !empty($model->cost_price)?$model->cost_price:0;
                },
            ],
            [
                'header'=>'Wholesale',
                'attribute' => 'wholesale_price',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'value'=>function ($model, $key, $index, $widget) {
                    return !empty($model->wholesale_price)?$model->wholesale_price:0;
                },
            ],
            [
                'header'=>'Retail',
                'attribute' => 'retail_price',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'value'=>function ($model, $key, $index, $widget) {
                    return !empty($model->retail_price)?$model->retail_price:0;
                },
            ],
            [
                'header'=>'Qty(Old)',
                'attribute' => 'previous_quantity',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'pageSummary' => "Total ",
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->previous_quantity;
                },
            ],
            [
                'header'=>'Qty(New)',
                'attribute' => 'new_quantity',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'pageSummary' => true,
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->new_quantity;
                },
            ],
            [
                'header'=>'Total',
                'attribute' => 'total_quantity',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->total_quantity;
                },
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'hidden'=>true,
                'hiddenFromExport'=>true,
                'header'=>'Action',
                'template'=>'{update} {details} ',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['stock-update','id'=>$model->product_stock_id]), [
                            'class'=>'btn btn-primary btn-xs',
                            'data-pjax'=>0,
                            'title' => Yii::t('app', 'Update Stock# '.$model->product_stock_id),
                        ]);
                    },

    //                'details' => function ($url, $model) {
    //                    return Html::button('<span class="glyphicon glyphicon-list"></span>', [
    //                        'class'=>'btn btn-success btn-xs modalUpdateBtn',
    //                        'title' => Yii::t('app', 'Product List '),
    //                        'data-pjax'=>1,
    //                        'value' =>Url::to(['product-stock-items/details','id'=>$model->product_stock_id])
    //                    ]);
    //                },
                ],

            ],


        ];

        $title = 'Stock Statement';
        if(Yii::$app->controller->id=='report'){
            $colspan = 16;
        }else{
            $colspan = 17;
        }

        $button = [
            Html::a(Yii::t('app', 'New Stock'),['create'], ['class' => 'btn btn-success', 'data-pjax'=>0]),
        ];
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>


</div>
