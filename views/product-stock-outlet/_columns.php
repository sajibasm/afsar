<?php

use app\components\DateTimeUtility;
use app\components\SystemSettings;
use app\components\Utility;
use app\models\ProductStockOutlet;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'createdAt',
        'pageSummary' => false,
        'hAlign' => GridView::ALIGN_CENTER,
        'contentOptions' => ['style' => 'width:80px;'],
        'value' => function ($model) {
            if(Yii::$app->controller->id == 'reports'){
                return DateTimeUtility::getDate($model->createdAt, SystemSettings::dateTimeFormat());
            }
            return DateTimeUtility::getDate($model->createdAt, 'h:i A');
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'invoice',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        'format' => 'raw',
        'value' => function ($model) {
            switch ($model->type) {
                case 'Transfer':
                    return '<span class="badge" style="background-color: #17a2b8; color: #fff;">Transfer</span>';
                case 'Received':
                    return '<span class="badge" style="background-color: #28a745; color: #fff;">Received</span>';
                case 'Movement':
                    return '<span class="badge" style="background-color: #ffc107; color: #212529;">Movement</span>';
                default:
                    return '<span class="badge badge-secondary">' . ucfirst($model->type) . '</span>';
            }
        },
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
        'attribute' => 'status',
        'format' => 'raw', // allows HTML output
        'hiddenFromExport' => true,
        'value' => function ($model) {
            switch (strtolower($model->status)) {
                case 'active':
                    return '<span class="badge" style="background-color: #28a745; color: #fff;">Active</span>';
                case 'inactive':
                    return '<span class="badge" style="background-color: #6c757d; color: #fff;">Inactive</span>';
                case 'reject':
                    return '<span class="badge" style="background-color: #dc3545; color: #fff;">Rejected</span>';
                default:
                    return '<span class="badge" style="background-color: #adb5bd; color: #212529;">' . ucfirst($model->status) . '</span>';
            }
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'template' => Helper::filterActionColumn('{approve} {reject} {details} {print}'),
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => Utility::encrypt($key)]);
        },
        'buttons' => [
            'print' => function ($url, $model) {
                if ($model->status !== ProductStockOutlet::STATUS_PENDING) {
                    return Html::a(
                        '<span class="fas fa-print"></span>',
                        $url,
                        [
                            'title' => 'Print',
                            'class' => 'btn btn-default btn-xs',
                            'target' => '_blank',
                            'data-pjax'=>0
                            //'data-toggle' => 'tooltip'
                        ]
                    );
                }
            },

            'details' => function ($url, $model) {
                return Html::button('<span class="fas fa-list"></span>', [
                    'class' => 'btn btn-success btn-xs modalUpdateBtn',
                    'title' => Yii::t('app', 'Product List '),
                    'data-pjax' => 1,
                    'value' => Url::to(['details', 'id' => Utility::encrypt($model->product_stock_outlet_id)])
                ]);
            },

            'approve' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return Html::a(
                        '<span class="fas fa-check"></span>',
                        'javascript:void(0);',
                        [
                            'title' => 'Approve',
                            'class' => 'btn btn-success btn-xs',
                            'data-toggle' => 'tooltip',
                            'onclick' => "
                    Swal.fire({
                        title: 'Are you sure you want to approve this item?',
                        text: 'This action cannot be undone.',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Yes, approve it!',
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
                                url: '{$url}',
                                success: function(response) {
                                    if (!response.error) {
                                        $.pjax.reload({container: '#productStockStoreIndex', timeout: 10000});
                                        showMessage('success', response.message || 'Item approved successfully');
                                    } else {
                                        showMessage('error', response.message || 'Failed to approve item');
                                    }
                                },
                                error: function() {
                                    showMessage('error', 'An unexpected error occurred.');
                                }
                            });
                        }
                    });
                    return false;
                "
                        ]
                    );
                }
            },


            'reject' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return Html::a(
                        '<span class="fas fa-times-circle"></span>',
                        'javascript:void(0);',
                        [
                            'title' => 'Reject',
                            'class' => 'btn btn-danger btn-xs',
                            'data-toggle' => 'tooltip',
                            'onclick' => "
                    Swal.fire({
                        title: 'Are you sure you want to reject this item?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Yes, reject it!',
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
                                url: '{$url}',
                                success: function(response) {
                                    if (!response.error) {
                                        $.pjax.reload({container: '#productStockStoreIndex', timeout: 10000});
                                        showMessage('success', response.message || 'Item rejected successfully');
                                    } else {
                                        showMessage('error', response.message || 'Failed to reject item');
                                    }
                                },
                                error: function() {
                                    showMessage('error', 'An unexpected error occurred.');
                                }
                            });
                        }
                    });
                    return false;
                "
                        ]
                    );
                }
            },

        ],

        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
    ],

];   
