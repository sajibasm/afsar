<?php

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
            if ($model->is_2fa_enabled) {
                return '<span class="badge bg-green-active" title="2FA Enabled"><i class="fas fa-lock"></i></span>';
            } else {
                return '<span class="badge bg-yellow-active" title="2FA Disabled"><i class="fas fa-unlock-alt"></i></span>';
            }
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($model) {
            $statusLabels = ConstrainUtility::USER_STATUS_LIST;
            $label = $statusLabels[$model->status] ?? 'Unknown';
            switch ($model->status) {
                case 10:
                    $badgeClass = 'bg-green'; // Active
                    break;
                case 1:
                    $badgeClass = 'bg-secondary'; // Inactive
                    break;
                case 2:
                    $badgeClass = 'bg-danger'; // Suspended
                    break;
                default:
                    $badgeClass = 'bg-dark'; // Unknown/fallback
            }

            return "<span class='badge {$badgeClass}'>{$label}</span>";
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
                return Html::a('<span class="fas fa-eye"></span>', ['view', 'id'=> Utility::encrypt($model->user_id)],
                    [
                        'class'=>'btn btn-default btn-xs',
                        'data-ajax'=>0,
                        'data-toggle'=>'tooltip',
                        'title'=>'View '.$model->username,
                    ]
                );
            },
            'update' => function ($url, $model) {
                return Html::a('<span class="fas fa-pen"></span>', ['update', 'id'=> Utility::encrypt($model->user_id)],
                    [
                        'class'=>'btn btn-warning btn-xs',
                        'data-ajax'=>0,
                        'data-toggle'=>'tooltip',
                        'title'=>'Update '.$model->username,
                    ]
                );
            },
            'store' => function ($url, $model) {
                return Html::a('<span class="fas fa-store"></span>', ['store', 'id'=> Utility::encrypt($model->user_id)],
                    [
                        'class'=>'btn btn-info btn-xs',
                        'data-ajax'=>0,
                        'data-toggle'=>'tooltip',
                        'title'=>'Assign To Store'
                    ]
                );
            },

            'delete' => function ($url, $model) {
                return Html::a(
                    '<span class="fa fa-trash"></span>',
                    ['delete', 'id' => Utility::encrypt($model->id)],
                    [
                        'class' => 'btn btn-danger btn-xs',
                        'data-method' => 'post', // Sends POST request
                        'data-confirm' => 'Are you sure you want to delete this item?',
                        'data-pjax' => '0',
                        'title' => Yii::t('app', 'User Delete'),
                    ]
                );
            },
        ],

    ],

];   