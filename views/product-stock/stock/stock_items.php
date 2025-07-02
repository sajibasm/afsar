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
            [
                'attribute'=>'cost_price',
                'header'=>'Cost',
                'value'=>function($data){
                    return $data->cost_price;
                }
            ],
            [
                'attribute'=>'wholesale_price',
                'header'=>'Wholesale',
                'value'=>function($data){
                    return $data->wholesale_price;
                }
            ],
            [
                'attribute'=>'retail_price',
                'header'=>'Retail',
                'value'=>function($data){
                    return $data->retail_price;
                }
            ],
            [
                'attribute'=>'new_quantity',
                'header'=>'New Qty',
                'value'=>function($data){
                    return $data->new_quantity;
                }
            ],
            [
                'attribute'=>'alert_quantity',
                'header'=>'Alert Qty',
                'value'=>function($data){
                    return $data->alert_quantity;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Action',
                'template'=>'{delete}',
                //'template'=>'{delete} {update}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        $deleteUrl = Url::to(['/product-stock/stock-delete']) . "?id=" . $model->product_stock_items_draft_id;

                        return Html::a('<span class="fas fa-trash"></span>', '#', [
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'btn btn-danger btn-xs',
                            'onclick' => "
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal2-confirm-popup',
                    title: 'swal2-confirm-title',
                    htmlContainer: 'swal2-confirm-text',
                    confirmButton: 'swal2-confirm-btn',
                    cancelButton: 'swal2-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '$deleteUrl',
                        success: function(response) {
                            if (!response.error) {
                                $.pjax.reload({container: '#stock', timeout: 10000});
                                showMessage('success', response.message || 'Item deleted successfully');
                            } else {
                                showMessage('error', response.message || 'Failed to delete item');
                            }
                        },
                        error: function() {
                            showMessage('error', 'An unexpected error occurred.');
                        }
                    });
                }
            });
            return false;
        ",
                        ]);
                    },


                    'update' => function ($url, $model) {
                        return Html::button('<span class="fas fa-pen"></span>', [
                            'class'=>'btn btn-warning btn-xs modalUpdateBtn',
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




