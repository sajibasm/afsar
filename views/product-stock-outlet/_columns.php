<?php

use app\components\Utility;
use app\models\ProductStockOutlet;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'invoice',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'transferOutlet',
        'value' => function ($model) {
            if ($model->transferFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                return ProductStockOutlet::TRANSFER_FROM_STOCK;
            } else {
                return (!empty($model->transferOutlet)) ? $model->transferOutletDetail->name : '';
            }
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'receivedOutlet',
        'value' => function ($model) {
            if ($model->receivedFrom === ProductStockOutlet::TRANSFER_FROM_STOCK) {
                return ProductStockOutlet::TRANSFER_FROM_STOCK;
            } else {
                return (!empty($model->receivedOutlet)) ? $model->receivedOutletDetail->name : '';
            }
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'transferBy',
        'value' => function ($model) {
            return (!empty($model->transferBy)) ? $model->transferByUser->username : '';
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'receivedBy',
        'value' => function ($model) {
            return (!empty($model->receivedBy)) ? $model->receivedByUser->username : '';
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'createdAt',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'template' => '{details} {view} {approve} {reject} {print}',
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => Utility::encrypt($key)]);
        },
        'buttons' => [

            'print' => function ($url) {
                return Html::a(
                    '<span class="glyphicon glyphicon-print"></span>',
                    $url,
                    [
                        'title' => 'Print',
                        'target' => '_blank',
                        'data-pjax'=>0
                        //'data-toggle' => 'tooltip'
                    ]
                );
            },

            'details' => function ($url) {
                return Html::a(
                    '<span class="glyphicon glyphicon-th"></span>',
                    $url,
                    [
                        'title' => 'Items',
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip'
                    ]
                );
            },

            'approve' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-ok"></span>',
                        $url,
                        [
                            'title' => 'Approve',
                            'role' => 'modal-remote',
                            'data-toggle' => 'tooltip'
                        ]
                    );
                }
            },

            'reject' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-remove"></span>',
                        $url,
                        [
                            'title' => 'Reject',
                            'role' => 'modal-remote',
                            'data-toggle' => 'tooltip'
                        ]
                    );
                }
            },
        ],

        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
    ],

];   
