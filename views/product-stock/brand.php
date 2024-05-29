<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Sold By Brand');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'stock_sold_by_brand'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php
    Utility::gridViewModal($this, $searchModel, '_search_brand');
    Utility::getMessage();
?>



<div class="product-stock-index">


    <?php
        $gridColumns = [
            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#'
            ],

            [
                'header'=>'Date',
                'contentOptions' => ['style' => 'width:100px;'],
                'value' => function ($model) {
                    return DateTimeUtility::getDate($model->sales->created_at, SystemSettings::getDateFormat());
                }
            ],

            [
                'header'=>'Customer',
                'attribute' => 'client_name',
                'value'=>function($model){
                    return $model->sales->client_name;
                },
            ],


            [
                'header'=>'Invoice',
                'attribute' => 'invoice_no',
                'contentOptions' => ['style' => 'width:100px;'],
                'value'=>function($model){
                    return $model->sales->sales_id;
                },
            ],

            [
                'attribute' => 'brand',
                'contentOptions' => ['style' => 'width:100px;'],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->brand->brand_name;
                }
            ],
            [
                'attribute' => 'item',
                'contentOptions' => ['style' => 'width:100px;'],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->item->item_name;
                },
            ],

            [
                'attribute' => 'size',
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummary' => "Total ",
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->size->size_name;
                }
            ],


            [
                'header'=>'Qty',
                'attribute' => '',
                'hAlign'=>'right',
                'format'=>['decimal', 0],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->quantity;
                },

                'pageSummary' => true,
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummaryOptions' => [
                    'prepend' => ''
                ]

            ],

            [
                'header'=>'Sales',
                'attribute' => 'sales_amount',
                'hAlign'=>'right',
                'pageSummary' => true,
                'format'=>['decimal', 0],
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummaryOptions' => ['prepend' => ''],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model->sales_amount;
                },
            ],


            [
                'header'=>'Total',
                'attribute' => 'total_amount',
                'hAlign'=>'right',
                'pageSummary' => true,
                'format'=>['decimal', 0],
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummaryOptions' => ['prepend' => ''],

                'value'=>function ($model, $key, $index, $widget) {
                    return $model->total_amount;
                },
            ],

        ];

        $title = 'Brand Wise Report';
        if(Yii::$app->controller->id=='report'){
            $colspan = 10;
        }else{
            $colspan = 10;
        }

        $button = 'New Stock';

        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>


</div>
