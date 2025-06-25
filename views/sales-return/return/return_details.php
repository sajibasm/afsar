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

    <?php
    $totalQuantity = 0;
    $totalAmount = 0;

    foreach ($dataProvider->models as $model) {
        $totalQuantity += $model->quantity;
        $totalAmount += $model->total_amount;
    }
    ?>


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
                'footer'=>'Total',
                'value'=>function($model){
                    return $model->quantity;
                },
                //'footer'=> CommonUtility::pageTotal($dataProvider->models,'quantity'). 'Qty',
            ],

            [
                'header' => 'Total',
                'attribute' => 'total_amount',
                'value' => function ($model) {
                    return $model->total_amount;
                },
                'footer' => ($totalAmount), // Or use number_format
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Restore',
                'template'=>'{remove}',
                'buttons' => [
                    'remove' => function ($url, $model) {
                        return Html::a('<span class="fas fa-trash"></span>','#', [
                            'title' => \Yii::t('yii', 'Delete'),
                            'class'=>'btn btn-danger btn-xs',
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
        ],
    ]); ?>

</div>
