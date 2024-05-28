<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\Sales;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'Sales Statement');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'sales_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
$this->registerJs(Utility::getMessage(), View::POS_END, 'alert');

if (SystemSettings::invoiceAutoPrintWindow()) {
    $session = Yii::$app->session;
    $printInvoice = $session['salesInvoiceAutoPrint'];
    if (!empty($printInvoice)) {
        $printUrl = Url::to(['sales/print', 'id' => Utility::encrypt($printInvoice)]);
        $this->registerJs(
            'var win = window.open("'.$printUrl.'", "_blank");
                if (win) {
                //Browser has allowed it to be opened
                win.focus();
                } else {
                    //Browser has blocked it
                    alert("Please allow popups for this website");
                 }',
            View::POS_READY,
            'newWindowForInvoice'
        );
        $session['salesInvoiceAutoPrint'] = null;
    }
}

Utility::gridViewModal($this, $searchModel);
?>

<div class="sales-index">
    <?php
    if (Yii::$app->controller->id == 'reports') {
        $colSpan = 15;
    } else {
        $colSpan = 15;
    }

    $button = [];
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'created_at',
            'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'width:80px;'],
            'value' => function ($model) {
                return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'outletId',
            'contentOptions' => ['style' => 'width:50px;'],
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return ($model->outlet) ? $model->outlet->name : '';
            },
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'sales_id',
            'hAlign' => GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'width:85px;']
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'user_id',
            'pageSummary' => false,
            //'hiddenFromExport'=>true,
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return ($model->user) ? $model->user->username : '';
            },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'client_name',
            'pageSummary' => false,
            //'noWrap' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'width:150px;'],
            'value' => function ($model) {
                return $model->client_name . "\n{$model->client->clientCity->city_name}";
            },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'contact_number',
            'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'width:110px;'],
            'hiddenFromExport' => true,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'transport_name',
            'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'width:110px;'],
            'hiddenFromExport' => true,
            'value' => function ($model) {
                if (!empty($model->transport_name)) {
                    return $model->transport_name . "\nTracking ($model->tracking_number)";
                } else {
                    return '';
                }
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'payment_type',
            'contentOptions' => ['style' => 'width:50px;'],
            'hAlign' => GridView::ALIGN_CENTER,
            'pageSummary' => "Total ",
            'value' => function ($model) {
                return $model->paymentTypeModel->type;
            },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'paid_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'contentOptions' => ['style' => 'width:100px;'],
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ]

        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'due_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'contentOptions' => ['style' => 'width:100px;'],
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ]
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'discount_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'contentOptions' => ['style' => 'width:100px;'],
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ]
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Recon',
            'attribute' => 'reconciliation_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'contentOptions' => ['style' => 'width:100px;'],
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ]
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'sales_return_amount',
            'contentOptions' => ['style' => 'width:100px;'],
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ]
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Refund',
            'contentOptions' => ['style' => 'width:100px;'],
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'pageSummaryOptions' => ['prepend' => ''],
            'value' => function ($data) {
                if (($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount) > ($data->total_amount - $data->discount_amount)) {
                    return ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount) - ($data->total_amount - $data->discount_amount);
                } else {
                    return 0;
                }
            },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'received_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'contentOptions' => ['style' => 'width:100px;'],
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ]
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Total Dues',
            'contentOptions' => ['style' => 'width:100px;'],
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'pageSummaryOptions' => [
                'prepend' => ''
            ],
            'value' => function ($data) {
                if (($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount) > ($data->total_amount - $data->discount_amount)) {
                    return (($data->total_amount - $data->discount_amount) + ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount) - ($data->total_amount - $data->discount_amount)) - ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount);
                } else {
                    return ($data->total_amount - $data->discount_amount) - ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount);
                }
            },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'total_amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'contentOptions' => ['style' => 'width:120px;'],
            'format' => ['decimal', 0],
            'value' => function ($data) {
                if (($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount) > ($data->total_amount - $data->discount_amount)) {
                    return (($data->total_amount - $data->discount_amount)+($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount) - ($data->total_amount - $data->discount_amount)) - ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount);
                } else {
                    return ($data->total_amount - $data->discount_amount) - ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount);
                }
            },
            'pageSummaryOptions' => [
                'prepend' => ''
            ],
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            //'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'width' => '180px',
            'hiddenFromExport' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'template' => '{print} {approved} {product} {payment} {transport} {notification} {update} {delete}',
            'buttons' => [
                'delete' => function ($url, $model) {
                    if(Yii::$app->user->can('/sales/remove-invoice')){
                        if ($model->status != Sales::STATUS_DELETE
                            && DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')
                            && $model->status == Sales::STATUS_APPROVED
                        ) {
                            return Html::a('<span class="fa fa-remove"></span>', Url::to(['delete-invoice', 'id' => Utility::encrypt($model->sales_id)]), [
                                'class' => 'btn btn-danger btn-xs approvedButton',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Delete ' . $this->title . '# ' . $model->sales_id),
                            ]);
                        }
                    }
                },
                'approved' => function ($url, $model) {
                    if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_PENDING ) {
                        return Html::a('<span class="fa fa-check"></span>', Url::to(['sales/view', 'id' => Utility::encrypt($model->sales_id)]), [
                            'class' => 'btn btn-default btn-xs approvedButton',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->sales_id),
                        ]);
                    }
                },
                'update' => function ($url, $model) {
                    if ($model->status != Sales::STATUS_PENDING && $model->status != Sales::STATUS_DELETE && Yii::$app->user->can('/sales/update')) {
                        if (
                            (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')
                                && Yii::$app->controller->id != 'reports')
                            || ($model->type == Sales::TYPE_SALES || $model->type == Sales::TYPE_SALES_UPDATE)
                            && (Yii::$app->controller->id != 'reports')
                        ) {
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['sales/update', 'id' => Utility::encrypt($model->sales_id)]), [
                                'class' => 'btn btn-warning btn-xs',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Update Invoice# ' . $model->sales_id . ' Customer: ' . $model->client_name),
                            ]);
                        }
                    }
                },
                'product' => function ($url, $model) {
                    return Html::button('<span class="fa fa-product-hunt"></span>', [
                            'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', 'Item Details.'),
                            'data-pjax' => 0,
                            'value' => Url::to(['sales-details/details', 'id' => Utility::encrypt($model->sales_id)])
                        ]) . '</li>';
                },
                'payment' => function ($url, $model) {
                    if ($model->status != Sales::STATUS_DELETE) {
                        return Html::button('<span class="fa fa-credit-card-alt"></span>', [
                            'class' => 'btn btn-success btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', 'Payment Details'),
                            'data-pjax' => 0,
                            'value' => Url::to(['customer-account/details', 'id' => Utility::encrypt($model->sales_id)])
                        ]);
                    }
                },
                'transport' => function ($url, $model) {
                    if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_APPROVED && Yii::$app->user->can('/sales/transport')) {

                        return Html::button('<span class="fa fa-truck"></span>', [
                            'class' => 'btn btn-default btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', 'Transport Details'),
                            'data-pjax' => 0,
                            'value' => Url::to(['sales/transport', 'id' => Utility::encrypt($model->sales_id)])
                        ]);

                    }
                },
                'notification' => function ($url, $model) {
                    if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_APPROVED && Yii::$app->user->can('/sales/notification')) {
                        return Html::button('<span class="fa fa-paper-plane"></span>', [
                            'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', 'Email/SMS Notification'),
                            'data-pjax' => 0,
                            'value' => Url::to(['sales/notification', 'id' => Utility::encrypt($model->sales_id)])
                        ]);
                    }
                },
                'print' => function ($url, $model) {
                    if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_APPROVED && Yii::$app->user->can('/sales/print')) {
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['sales/print', 'id' => Utility::encrypt($model->sales_id)]), [
                            'class' => 'btn btn-success btn-xs',
                            'title' => Yii::t('app', 'Print Invoice'),
                            'data-pjax' => 0,
                            'target' => '_blank'
                        ]);
                    }
                }
            ]
        ]
    ];

    yii\widgets\Pjax::begin(['id' => 'salesPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colSpan, $exportFileName);
    yii\widgets\Pjax::end();
    ?>


</div>
