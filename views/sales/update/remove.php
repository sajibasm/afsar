<?php

use app\components\Utility;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="sales-draft-update-removed">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'item_id',
                'header'=>'Item',
                'value'=>function($data){
                    return $data->item->item_name;
                }
            ],

            [
                'attribute'=>'brand_id',
                'header'=>'Brand',
                'value'=>function($data){
                    return $data->brand->brand_name;
                }
            ],
            [
                'attribute'=>'size_id',
                'header'=>'Size',
                'value'=>function($data){
                    return $data->size->size_name;
                }
            ],
            [
                'attribute'=>'sales_amount',
                'header'=>'Unit Price',
                'value'=>function($data){
                    return $data->sales_amount.' '.Yii::$app->params['currency'];
                }
            ],
            [
                'attribute'=>'quantity',
                'header'=>'Quantity',
                'value'=>function($data){
                    return $data->quantity;
                }
            ],
            [
                'attribute'=>'total_amount',
                'header'=>'Total',
                'value'=>function($data){
                    return $data->total_amount.' '.Yii::$app->params['currency'];
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Action',
                'template'=>'{restore}',
                'buttons' => [
                    'restore' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-upload"></span>','#', [
                            'title' => \Yii::t('yii', 'Restore'),
                            'class'=>'btn btn-primary btn-xs',
                            'onclick'=>"
                             if (confirm('Are you sure you want to restore this?')) {
                                $.ajax({
                                type     :'GET',
                                cache    : false,
                                url  : '".Url::to(['/sales/invoice-item-update-restore'])."?id=".Utility::encrypt($model->sales_details_id)."',
                               
                                beforeSend: function( xhr ) {
                                        $('#loading').show();
                                },
                               
                                success  : function(response) {
                                        $('#loading').hide();
                                      $.pjax.reload ({container: '#sellUpdate', 'timeout': 10000});
                                    }
                              
                                });
                            }
                            return false;",
                        ]);

                    },
                ],
            ]

        ],
    ]); ?>
</div>