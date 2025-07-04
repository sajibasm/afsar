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

            // ✅ Print Button
            'print' => function ($url, $model) {
                if ($model->status !== ProductStockOutlet::STATUS_PENDING) {
                    return \app\components\ButtonHelper::actionButton('print', $url, [
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Print'),
                    ]);
                }
            },

            // ✅ Details Button (Modal)
            'details' => function ($url, $model) {
                return \app\components\ButtonHelper::actionButton('details', '#', [
                    'value' => Url::to(['details', 'id' => Utility::encrypt($model->product_stock_outlet_id)]),
                    'class' => 'modalUpdateBtn',
                    'data-pjax' => 1,
                    'title' => Yii::t('app', 'Product List'),
                ]);
            },

            // ✅ Approve Button (AJAX + SweetAlert)
            'approve' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return \app\components\ButtonHelper::actionButton('approve', $url, [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure you want to approve this item?',
                        'confirmText' => 'This action cannot be undone.',
                        'confirmButton' => 'Yes, approve it!',
                        'cancelButton' => 'Cancel',
                        'confirmAjax' => 1,
                        'pjaxId' => '#productStockStoreIndex',
                        'title' => Yii::t('app', 'Approve'),
                    ]);
                }
            },

            // ✅ Reject Button (AJAX + SweetAlert)
            'reject' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return \app\components\ButtonHelper::actionButton('reject', $url, [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure you want to reject this item?',
                        'confirmText' => 'This action cannot be undone.',
                        'confirmButton' => 'Yes, reject it!',
                        'cancelButton' => 'Cancel',
                        'confirmAjax' => 1,
                        'pjaxId' => '#productStockStoreIndex',
                        'title' => Yii::t('app', 'Reject'),
                    ]);
                }
            },

        ],
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
    ],


];   
