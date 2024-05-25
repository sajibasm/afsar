<?php
use app\components\CommonUtility;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockItemsDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="product-stock-items-draft-index">
       <?= Html::hiddenInput('total', CommonUtility::getTotalStockDraftItems(),['id'=>'totalItem']);?>
        <div class="alert alert-danger" id="cartError" style="display: none; text-align: center" role="alert">Empty Cart cannot be save.</div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
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
//            [
//                'attribute'=>'cost_price',
//                'header'=>'Cost',
//                'value'=>function($data){
//                    return $data->cost_price;
//                }
//            ],
//            [
//                'attribute'=>'wholesale_price',
//                'header'=>'Wholesale',
//                'value'=>function($data){
//                    return $data->wholesale_price;
//                }
//            ],
//            [
//                'attribute'=>'retail_price',
//                'header'=>'Retail',
//                'value'=>function($data){
//                    return $data->retail_price;
//                }
//            ],
            [
                'attribute'=>'new_quantity',
                'header'=>'New Qty',
                'value'=>function($data){
                    return $data->new_quantity;
                }
            ],
//            [
//                'attribute'=>'alert_quantity',
//                'header'=>'Alert Qty',
//                'value'=>function($data){
//                    return $data->alert_quantity;
//                }
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Action',
                'template'=>'{delete}',
                //'template'=>'{delete} {update}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>','#', [
                            'title' => \Yii::t('yii', 'Delete'),
                            'class'=>'btn btn-warning btn-xs',
                            'onclick'=>"
                             if (confirm('Are you sure you want to delete this?')) {
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '".Url::to(['/product-stock/stock-delete'])."?id=".$model->product_stock_items_draft_id."&action=".Yii::$app->controller->action->id."',
                                success  : function(response) {
                                           $.pjax.reload ({container: '#stock', 'timeout': 10000});
                                }
                                });
                            }
                            return false;",
                        ]);

                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-edit"></span>', [
                            'class'=>'btn btn-info btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', $model->item->item_name.' Update'),
                            'id'=>'modalUpdateBtn1',
                            'data-pjax'=>1,
                            'value' =>Url::to(['item-update','id'=>$model->product_stock_items_draft_id])
                        ]);
                    },
                ],
            ],


        ],
    ]); ?>

</div>




