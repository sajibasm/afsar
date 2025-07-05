<?php

use app\components\ButtonHelper;
use app\components\ConstrainUtility;
use app\components\Utility;
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
        'attribute' => 'first_name',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'last_name',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'username',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Store',
        'attribute' => 'user.userOutletDetail',
        'format' => 'raw', // Important to render HTML
        'value' => function ($model) {
            $badges = '';
            foreach ($model->userOutletDetail as $outletModel) {
                $storeName = $outletModel->outletDetail->name ?? 'N/A';
                $badges .= "<span class='badge bg-blue-active' style='margin-right:5px;'>{$storeName}</span>";
            }
            return $badges ?: "<span class='badge bg-dark'>No Store</span>";
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'email',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'user_image',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'is_2fa_enabled',
        'format' => 'raw',
        'label' => '2FA',
        'hAlign' => 'center',
        'value' => function ($model) {
            $status = $model->is_2fa_enabled ? '2fa-enabled' : '2fa-disabled';
            return \app\components\BadgeHelper::render($status);
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($model) {
            return \app\components\BadgeHelper::render($model->status);
        },
        'hAlign' => 'center',
        'label' => 'Status',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_at',
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        //'hidden'=>true,
        'hiddenFromExport'=>true,
        'hAlign'=>GridView::ALIGN_CENTER,
        'contentOptions' => ['style' => 'white-space: nowrap;'],
        'template' => Helper::filterActionColumn('{view} {update} {store} {delete}'),
        'buttons' => [

            'view' => function ($url, $model) {
                return ButtonHelper::actionButton('view', ['view', 'id' => Utility::encrypt($model->user_id)], [
                    'title' => 'View ' . $model->username,
                    'class' => '',  // Optional: add extra class if needed
                    'data-pjax' => 0,
                ]);
            },

            'update' => function ($url, $model) {
                return ButtonHelper::actionButton('update', ['update', 'id' => Utility::encrypt($model->user_id)], [
                    'title' => 'Update ' . $model->username,
                    'data-pjax' => 0,
                ]);
            },

            'store' => function ($url, $model) {
                return ButtonHelper::actionButton('store', ['store', 'id' => Utility::encrypt($model->user_id)], [
                    'title' => 'Assign To Store',
                    'data-pjax' => 0,
                ]);
            },

            'delete' => function ($url, $model) {
                return ButtonHelper::actionButton('delete', ['delete', 'id' => Utility::encrypt($model->id)], [
                    'title' => Yii::t('app', 'User Delete'),
                    'confirm' => true,  // SweetAlert confirm
                    'confirmText' => 'Are you sure you want to delete this item?',
                    'data-pjax' => 0,
                    'data-method' => 'post',  // ✅ Ensure it sends POST
                ]);
            },

        ],

    ],

];   