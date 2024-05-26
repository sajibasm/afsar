<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\MarketBook;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\MarketBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model  app\models\MarketBook */
$this->title = Yii::t('app', 'Market Book');
$exportFileName = 'market_book_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>
<?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


    <?php

        $gridColumns = [
            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#',
                'hiddenFromExport'=>true,
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
                'header'=>'Remarks',
                'attribute' => 'remarks',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Status',
                'attribute' => 'status',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Price',
                'attribute' => 'sales_amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>"Total",
                'format'=>['decimal',2],
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Qty',
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
                'pageSummary' =>"",
                'format'=>['decimal',2],
            ],


            [
                'class'=>'kartik\grid\ActionColumn',
                //'hidden'=>true,
                'vAlign'=>GridView::ALIGN_RIGHT,
                'hiddenFromExport'=>true,
                'hAlign'=>GridView::ALIGN_CENTER,
                //'hidden'=>Yii::$app->controller->id=='report'?false:true,
                'template'=>'{update}',
                'buttons' => [

                    'update' => function ($url, $model) {
                        $availableQty = CustomerUtility::marketReturnableQty($model->client_id, $model->size_id);

                        if($model->status==MarketBook::STATUS_SELL && $availableQty>0) {
                            return Html::button('<span class="glyphicon glyphicon-export"></span>', [
                                'class' => 'btn btn-info btn-xs modalUpdateBtn',
                                'title' => Yii::t('app', 'Return Qty '.$model->item->item_name),
                                'data-toggle'=>'tooltip',
                                'id' => 'modalUpdateBtn1',
                                'data-pjax' => 0,
                                'value' => Url::to(['market-book/draft-update', 'id' => Utility::encrypt($model->market_sales_id)])
                            ]);
                        }
                    },

                    'return' => function ($url, $model) {

                        if($model->status==MarketBook::STATUS_SELL){

                            return Html::a('<span class="glyphicon glyphicon-export"></span>','#', [
                                'title' => \Yii::t('yii', 'Return'),
                                'class'=>'btn btn-danger btn-xs',
                                'onclick'=>"
                                if (confirm('Are you sure you want to Return this Item?')) {
                                $.ajax({
                                type     :'GET',
                                cache    : false,
                                url  : '".Url::to(['/market-book/return-item'])."?id=".Utility::encrypt($model->market_sales_id)."',
                                success  : function(response) {
                                      $.pjax.reload ({container: '#marketSell', 'timeout': 10000});
                                 }

                                });
                            }
                            return false;",
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

        $button = null;

        //yii\widgets\Pjax::begin(['id'=>'marketSell']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName, false, false, false);
        //yii\widgets\Pjax::end();



?>
