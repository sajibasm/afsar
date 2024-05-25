<?php

use app\components\CommonUtility;
use app\components\Utility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReturnDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="return-draft-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,

        'layout' => '{items}{pager}',
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'font-weight:bold;'],

        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'#',
                'template'=>'{remove}',
                'buttons' => [
                    'remove' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>','#', [
                            'title' => \Yii::t('yii', 'Delete'),
                            'class'=>'btn btn-warning btn-xs',
                            'onclick'=>"
                             if (confirm('Do you want to remove this item from cart?')) {
                                $.ajax({
                                type     :'GET',
                                cache    : false,
                                url  : '".Url::to(['/sales-return/items-remove'])."?id=". Utility::encrypt($model->return_draft_id)."',
                                success  : function(response) {

                                      $.pjax.reload ({container: '#returnCart', 'timeout': 10000});

                                 }

                                });
                            }
                            return false;",
                        ]);

                    },
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
                'attribute'=>'refund_amount',
                'value'=>function($model){
                    return $model->refund_amount;
                },
                //'footer'=>'Total',
            ],
            [
                'header'=>'Quantity',
                'attribute'=>'quantity',
                'value'=>function($model){
                    return $model->quantity;
                },
                //'footer'=> CommonUtility::pageTotal($dataProvider->models,'quantity'). 'Qty',
            ],

            [
                'header'=>'Total',
                'attribute'=>'total_amount',
                'value'=>function($model){
                    return $model->total_amount;
                },
                //'footer'=> CommonUtility::pageTotal($dataProvider->models,'total_amount'). 'BDT',
            ],
        ],
    ]); ?>

</div>
