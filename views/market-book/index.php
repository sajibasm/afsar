<?php

use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MarketBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Market Books');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'market_book_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="market-book-index">

    <?php

        $gridColumns = [
            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#',
                'hiddenFromExport'=>true,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Customer',
                'attribute' => 'client_id',
                'pageSummary' => false,
                'hAlign'=>GridView::ALIGN_CENTER,
                'group'=>true,  // enable grouping
                'value'=>function($data){
                    return $data->client->client_name;
                },

            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Date',
                'attribute' => 'created_at',
                'hAlign'=>GridView::ALIGN_CENTER,
                'group'=>true,  //enable grouping
                'subGroupOf'=>1, // StockId column index is the parent group,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Item',
                'attribute' => 'item_id',
                'pageSummary' => false,
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($data){
                    return $data->item->item_name;
                },

            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Brand',
                'attribute' => 'brand_id',
                'pageSummary' => false,
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($data){
                    return $data->brand->brand_name;
                },

            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Size',
                'attribute' => 'size_id',
                'pageSummary' => false,
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->size->size_name;
                },
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Status',
                'attribute' => 'status',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],


            /*            [
                            'class' => '\kartik\grid\DataColumn',
                            'header'=>'Cost',
                            'attribute' => 'cost_amount',
                            'hAlign'=>GridView::ALIGN_RIGHT,
                            'pageSummary' =>true,
                            'format'=>['decimal',2],
                        ],*/

            [
                'class' => '\kartik\grid\DataColumn',
                //'header'=>'Sales Amount',
                'attribute' => 'sales_amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',2],
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Quantity',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',2],
                'value'=>function($data){
                    return $data->quantity;
                },
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Total',
                'attribute' => 'total_amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',2],
            ],


            [
                'class'=>'kartik\grid\ActionColumn',
                //'hidden'=>true,
                'vAlign'=>GridView::ALIGN_RIGHT,
                'hiddenFromExport'=>true,
                'hAlign'=>GridView::ALIGN_CENTER,
                'hidden'=>Yii::$app->controller->id=='report'?false:true,
                'template'=>'{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        if(DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y') && Yii::$app->controller->id!=='reports'){
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['sales/update','id'=>$model->market_sales_id]),[
                                'class'=>'btn btn-primary btn-xs',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Update Invoice# '.$model->market_sales_id. ' Customer: '),
                            ]);
                        }
                    },
                ],

            ],

        ];

        if(Yii::$app->controller->id=='report'){
            $colspan = 11;
        }else{
            $colspan = 12;
        }

        $button = [
            Html::a(Yii::t('app', 'Create Invoice'),['generate-invoice'], ['class' => 'btn btn-info', 'data-pjax'=>0])
        ];

        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>



</div>
