<?php

use app\components\ButtonHelper;
use app\components\Utility;
use app\models\SalesDraft;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="sales-draft-index">


    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
//        'pjaxSettings' => [
//            'neverTimeout' => true,
//            'options' => ['id' => 'SellDraft'],
//        ],
        'hover' => true,
        'striped' => true,
        'bordered' => true,
        'responsive' => true,
        'layout' => '{items}',
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'item_id',
                'label' => 'Item',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'value' => function($data){
                    return $data->item->item_name;
                }
            ],

            [
                'attribute' => 'brand_id',
                'label' => 'Brand',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'value' => function($data){
                    return $data->brand->brand_name;
                }
            ],

            [
                'attribute' => 'size_id',
                'label' => 'Size',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'value' => function($data){
                    return $data->size->size_name;
                }
            ],

            // Editable Price Column
            [
                'class' => EditableColumn::class,
                'attribute' => 'sales_amount',
                'label' => 'Unit Price',
                'hAlign' => 'right',
                'vAlign' => 'middle',
                'width' => '100px',
                'format' => ['decimal', 2],
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'name' => 'sales_amount',
                        'asPopover' => true,
                        'value' => $model->sales_amount,
                        'formOptions' => ['action' => Url::to(['update-cart-item'])],
                        'pjaxContainerId' => 'SellDraft', // ✅ Required!
                        'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                        'options' => ['pluginOptions' => ['min' => 0, 'step' => 0.01]]
                    ];
                },
            ],

            // Editable Quantity Column
            [
                'class' => EditableColumn::class,
                'attribute' => 'quantity',
                'label' => 'Qty',
                'hAlign' => 'right',
                'vAlign' => 'middle',
                'width' => '100px',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'name' => 'quantity',
                        'asPopover' => true,
                        'value' => $model->quantity,
                        'formOptions' => ['action' => Url::to(['update-cart-item'])],
                        'pjaxContainerId' => 'SellDraft', // ✅ Required!
                        'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                        'options' => ['pluginOptions' => ['min' => 1, 'step' => 1]]
                    ];
                },
            ],

            [
                'attribute' => 'total_amount',
                'label' => 'Total',
                'hAlign' => 'right',
                'vAlign' => 'middle',
                'width' => '100px',
                'format' => ['decimal', 2],
                'value' => function($data){
                    return $data->total_amount;
                }
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{delete}',
                'header' => 'Action',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '100px',
                'buttons' => [
                    'add' => function ($url, $model, $key) {
                        return ButtonHelper::actionButton('add', '#', [
                            'class' => 'btn-confirm',
                            'confirmAjax' => true,
                            'confirm' => false,
                            'pjaxId' => '#sell',
                            'data-id' => Utility::encrypt($model->sales_details_id),
                            'url' => Url::to(['update-cart-item', 'type' => SalesDraft::CART_ITEM_INCREASE]),
                        ]);
                    },
                    'remove' => function ($url, $model, $key) {
                        return ButtonHelper::actionButton('remove', '#', [
                            'class' => 'btn-confirm',
                            'confirmAjax' => true,
                            'confirm' => false,
                            'pjaxId' => '#sell',
                            'data-id' => Utility::encrypt($model->sales_details_id),
                            'url' => Url::to(['update-cart-item', 'type' => SalesDraft::CART_ITEM_DECREASE]),
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return ButtonHelper::actionButton('delete', '#', [
                            'class' => 'btn-confirm',
                            'confirmAjax' => true,
                            'confirm' => true,
                            'confirmTitle' => 'Are you sure?',
                            'confirmText' => 'This will permanently delete the record.',
                            'confirmButton' => 'Yes, delete it!',
                            'cancelButton' => 'Cancel',
                            'pjaxId' => '#SellDraft',
                            'data-id' => Utility::encrypt($model->sales_details_id),
                            'url' => Url::to(['remove-cart-item']),
                        ]);
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>