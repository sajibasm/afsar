<?php

use app\components\ButtonHelper;
use app\components\DateTimeUtility;
use app\components\SystemSettings;
use app\components\Utility;
use app\models\CustomerWithdraw;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'ID',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->id;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'outletId',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->outletDetail->name;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Date',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Payment ID',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->payment_history_id;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Created',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return ($model->user) ? $model->user->username : '';
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Approved',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return ($model->updatedUser) ? $model->updatedUser->username : '';
        }
    ],


    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Remarks',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->remarks;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Status',
        'hAlign' => GridView::ALIGN_CENTER,
        'pageSummary' => "Total",
        'value' => function ($model) {
            return $model->status;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Type',
        'hAlign' => GridView::ALIGN_CENTER,
        'pageSummary' => "Total",
        'value' => function ($model) {
            return $model->type;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Amount',
        'hAlign' => GridView::ALIGN_RIGHT,
        'pageSummary' => true,
        'format' => ['decimal', 0],
        'value' => function ($model) {
            return $model->amount;
        }
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
        'width' => '120px',
        'vAlign' => GridView::ALIGN_RIGHT,
        'hiddenFromExport' => true,
        'hAlign' => GridView::ALIGN_CENTER,
        'template' => \mdm\admin\components\Helper::filterActionColumn('{print} {notification} {update} {approved}'),
        'buttons' => [
            'notification' => function ($url, $model) {
                if ($model->status == CustomerWithdraw::STATUS_APPROVED) {
                    return ButtonHelper::actionButton('notification', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Send Invoice Email?',
                        'confirmText' => 'Do you want to send this invoice to the customer via email?',
                        'confirmButton' => 'Yes, send it!',
                        'cancelButton' => 'No, cancel',
                        'class' => 'btn-confirm',
                        'url' => Url::to(['/customer-withdraw/notification']),
                        'confirmAjax' => 1,
                        'pjaxId' => '#salesPjaxGridView',
                        'data-id' => Utility::encrypt($model->id),
                        'title' => Yii::t('app', 'Send Invoice'),
                    ]);
                }
                return null;
            },
            'approved' => function ($url, $model) {
                if ($model->status == CustomerWithdraw::STATUS_PENDING) {
                    return ButtonHelper::actionButton('approve', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure you want to approve this payment?',
                        'confirmText' => 'This action cannot be undone.',
                        'confirmButton' => 'Yes, approve it!',
                        'cancelButton' => 'Cancel',
                        'url' => Url::to(['approve']),
                        'confirmAjax' => 1,
                        'data-id' => Utility::encrypt($model->id),   // ✅ Send ID separately
                        'pjaxId' => '#customerWithdrawPjaxGridView',
                        'title' => Yii::t('app', 'Approve Payment'),
                    ]);
                }
            },

            'print' => function ($url, $model) {
                return ButtonHelper::actionButton('print', Url::to(['print', 'id' => Utility::encrypt($model->id)]), [
                    'title' => Yii::t('app', 'Print Invoice'),
                ]);
            },

            'update' => function ($url, $model) {
                if($model->status == CustomerWithdraw::STATUS_PENDING){
                    return ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                        'title' => Yii::t('app', 'Update Invoice'),
                    ]);
                }
            }
        ],

    ],

];