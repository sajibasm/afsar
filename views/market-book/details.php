<?php

use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\MarketBook;

use yii\grid\GridView;
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


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
       'created_at',
       'item.item_name',
       'brand.brand_name',
       'size.size_name',
       'remarks',
       'status',
       'sales_amount',
       'quantity',
       'total_amount',
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {return}',
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

                    if($model->status==MarketBook::STATUS_SELL) {

                        return Html::a('<span class="glyphicon glyphicon-export"></span>', '#', [
                            'title' => \Yii::t('yii', 'Return'),
                            'class' => 'btn btn-danger btn-xs',
                            'onclick' => "
                                if (confirm('Are you sure you want to Return this Item?')) {
                                $.ajax({
                                type     :'GET',
                                cache    : false,
                                url  : '" . Url::to(['/market-book/return-item']) . "?id=" . Utility::encrypt($model->market_sales_id) . "',
                                success  : function(response) {
                                      $.pjax.reload ({container: '#marketSell', 'timeout': 10000});
                                 }

                                });
                            }
                            return false;",
                        ]);
                    }
                }
            ]
        ]
    ]
]); ?>






