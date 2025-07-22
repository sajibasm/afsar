<?php

use app\components\ButtonHelper;
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
            return \app\components\BadgeHelper::render($model->type);
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
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'format' => 'raw',
        'hAlign'=>GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return \app\components\BadgeHelper::render($model->status);
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
                    return ButtonHelper::actionButton('print', $url, [
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Print'),
                    ]);
                }
            },

            // ✅ Details Button (Modal)
            'details' => function ($url, $model) {
                return ButtonHelper::actionButton('details', '#', [
                    'value' => Url::to(['details', 'id' => Utility::encrypt($model->product_stock_outlet_id)]),
                    'class' => 'modalUpdateBtn',
                    'data-pjax' => 1,
                    'title' => Yii::t('app', 'Product List'),
                ]);
            },

            // ✅ Approve Button (AJAX + SweetAlert)
            'approve' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return ButtonHelper::actionButton('approve', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure?',
                        'confirmText' => 'Do you want to approve this?',
                        'confirmButton' => 'Yes, approve it!',
                        'cancelButton' => 'Cancel',
                        'class' => 'btn-confirm',  // ✅ Ensure btn-confirm is here for JS trigger
                        'url' => Url::to(['approve']),
                        'data-id' => Utility::encrypt($model->product_stock_outlet_id),   // ✅ Send ID separately
                        'confirmAjax' => 1,                        // ✅ Enable AJAX call in confirm-buttons.js
                        'pjaxId' => '#productStockStore',                 // ✅ Optional: PJAX container if you want to reload something
                        'title' => Yii::t('app', 'Approve action !'),
                    ]);
                }
            },

            // ✅ Reject Button (AJAX + SweetAlert)
            'reject' => function ($url, $model) {
                if ($model->status === ProductStockOutlet::STATUS_PENDING && $model->type === ProductStockOutlet::TYPE_RECEIVED) {
                    return ButtonHelper::actionButton('reject', 'javascript:;', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure you want to reject this item?',
                        'confirmText' => 'This action cannot be undone.',
                        'confirmButton' => 'Yes, reject it!',
                        'cancelButton' => 'Cancel',
                        'confirmAjax' => 1,
                        'pjaxId' => '#productStockStore',
                        'url' => \yii\helpers\Url::to(['reject', 'id' => Utility::encrypt($model->product_stock_outlet_id)]),  // ✅ Correct reject URL
                        'title' => Yii::t('app', 'Reject'),
                    ]);
                }
            },

        ],
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
    ],


];   
