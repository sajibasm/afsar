<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\SalesReturn;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
return
    [
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'sales_return_id',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'outletId',
            'value' => function ($model) {
                return $model->outletDetail->name;
            },
            'hAlign' => GridView::ALIGN_CENTER,
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
            'header' => 'User',
            'attribute' => 'user_id',
            //'pageSummary' =>"Total ",
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return ($model->user) ? $model->user->username : '';
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Invoice',
            'attribute' => 'sales_id',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Type',
            'attribute' => 'type',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Customer',
            'attribute' => 'client_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'remarks',
            'hAlign' => GridView::ALIGN_CENTER,
            'pageSummary' => "Total ",
            'filterType' => GridView::FILTER_DATE_RANGE
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'refund_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'cut_off_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'total_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'format' => ['decimal', 0],
            'pageSummary' => true,
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'hiddenFromExport' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'white-space: nowrap;'],
            'template' => '{approved} {product} {payment} ',
            'buttons' => [

                'approved' => function ($url, $model) {
                    if ($model->status == SalesReturn::STATUS_PENDING) {
                        return Html::a('<span class="fas fa-check"></span>', Url::to(['view', 'id' => Utility::encrypt($model->sales_return_id)]), [
                            'class' => 'btn btn-default btn-xs approvedButton',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->total_amount),
                        ]);
                    }
                },

                'product' => function ($url, $model) {
                    return Html::button('<span class="fas fa-box-open"></span>', [
                        'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                        'title' => Yii::t('app', 'Item Details.'),
                        'data-pjax' => 0,
                        'value' => Url::to(['sales-details/details', 'id' => Utility::encrypt($model->sales_return_id)])
                    ]);
                },

                'payment' => function ($url, $model) {

                    return Html::button('<span class="fas fa-credit-card"></span>', [
                        'class' => 'btn btn-success btn-xs modalUpdateBtn',
                        'title' => Yii::t('app', 'Payment Details'),
                        'data-pjax' => 0,
                        'value' => Url::to(['customer-account/details', 'id' => Utility::encrypt($model->sales_return_id)])
                    ]);

                },

                'print' => function ($url, $model) {
                    return Html::a('<span class="fas fa-print"></span>', Url::to(['sales/print', 'id' => Utility::encrypt($model->sales_return_id)]), [
                        'class' => 'btn btn-success btn-xs',
                        'title' => Yii::t('app', 'Print Invoice'),
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
                },
            ],

        ],

    ]

?>