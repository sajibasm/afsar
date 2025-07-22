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
        'hover' => true,
        'striped' => true,
        'bordered' => true,
        'responsive' => true,
        'layout' => '{items}',
        'rowOptions' => function ($model) {
            $styles = [];

            if ($model->type === \app\models\SalesDraft::TYPE_UPDATE_DELETED) {
                $styles[] = 'background-color: #fdecea;';
                $styles[] = 'color: #b71c1c;';
                $styles[] = 'text-decoration: line-through;';
            } elseif ($model->type === \app\models\SalesDraft::TYPE_UPDATE_MODIFIED) {
                $styles[] = 'background-color: #fff8e1;';
                $styles[] = 'color: #ff6f00;';
            } elseif ($model->type === \app\models\SalesDraft::TYPE_UPDATE) {
                $styles[] = 'background-color: #e8f5e9;';
                $styles[] = 'color: #2e7d32;';
            } elseif ($model->type === \app\models\SalesDraft::TYPE_UPDATE_ADDED) {
                $styles[] = 'background-color: #e3f2fd;';
                $styles[] = 'color: #1565c0;';
            }

            return ['style' => implode(' ', $styles)];
        },

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
            [
                'attribute' => 'type',
                'label' => 'Status',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'format' => 'raw',
                'value' => function ($model) {
                    switch ($model->type) {
                        case \app\models\SalesDraft::TYPE_UPDATE_DELETED:
                            $typeKey = 'delete';
                            break;
                        case \app\models\SalesDraft::TYPE_UPDATE_MODIFIED:
                            $typeKey = 'modified';
                            break;
                        case \app\models\SalesDraft::TYPE_UPDATE:
                            $typeKey = 'unchanged';
                            break;
                        case \app\models\SalesDraft::TYPE_UPDATE_ADDED:
                            $typeKey = 'added';
                            break;
                        default:
                            $typeKey = 'unknown';
                    }

                    return \app\components\BadgeHelper::render($typeKey);
                },
            ],
            // Editable Price Column
            [
                'class' => EditableColumn::class,
                'attribute' => 'sales_amount',
                'label' => 'Unit Price',
                'hAlign' => 'right',
                'vAlign' => 'middle',
                'width' => '100px',
                'format' => ['raw'], // Important to allow <del> rendering
                'value' => function($model) {
                    $formatted = Yii::$app->formatter->asDecimal($model->sales_amount, 2);
                    return $model->type === \app\models\SalesDraft::TYPE_UPDATE_DELETED
                        ? "<del>{$formatted}</del>" : $formatted;
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'name' => 'sales_amount',
                        'asPopover' => true,
                        'disabled' => $model->type === \app\models\SalesDraft::TYPE_UPDATE_DELETED, // ✅ disable edit
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
                'format' => ['raw'], // Important to allow <del> rendering

                'value' => function($model) {
                    $formatted = Yii::$app->formatter->asDecimal($model->quantity, 2);
                    return $model->type === \app\models\SalesDraft::TYPE_UPDATE_DELETED
                        ? "<del>{$formatted}</del>" : $formatted;
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'name' => 'quantity',
                        'disabled' => $model->type === \app\models\SalesDraft::TYPE_UPDATE_DELETED, // ✅ disable edit
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
                        if($model->type!==SalesDraft::TYPE_UPDATE_DELETED){
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
                        }
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>