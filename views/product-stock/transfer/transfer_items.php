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
    <?= Html::hiddenInput('total', CommonUtility::getTotalStockDraftItems(), ['id' => 'totalItem']); ?>
    <div class="alert alert-danger" id="cartError" style="display: none; text-align: center" role="alert">Empty Cart
        cannot be save.
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'item_id',
                'header' => 'Item',
                'value' => function ($data) {
                    return $data->item->item_name;
                }
            ],

            [
                'attribute' => 'brand_id',
                'header' => 'Brand',
                'value' => function ($data) {
                    return $data->brand->brand_name;
                }
            ],
            [
                'attribute' => 'size_id',
                'header' => 'Size',
                'value' => function ($data) {
                    return $data->size->size_name;
                }
            ],
            [
                'attribute' => 'new_quantity',
                'header' => 'New Qty',
                'value' => function ($data) {
                    return $data->new_quantity;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => '{delete}',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('delete', '#', [
                            'confirm' => true,
                            'confirmTitle' => 'Are you sure?',
                            'confirmText' => 'This action cannot be undone.',
                            'confirmButton' => 'Yes, delete it!',
                            'cancelButton' => 'Cancel',
                            'class' => 'btn-confirm',  // ✅ Required for JS trigger
                            'url' => Url::to(['/product-stock/stock-delete', 'id' => $model->product_stock_items_draft_id]),
                            'confirmAjax' => 1,        // ✅ Enables AJAX mode
                            'pjaxId' => '#stockTransfer',       // ✅ PJAX container to reload
                            'title' => Yii::t('yii', 'Delete'),
                        ]);
                    },
                ],
            ],

        ],
    ]); ?>

</div>




