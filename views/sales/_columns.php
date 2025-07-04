<?php

use app\components\ButtonHelper;
use app\components\DateTimeUtility;
use app\components\SystemSettings;
use app\components\Utility;
use app\models\Sales;
use app\models\Transport;
use kartik\grid\GridView;
use kartik\typeahead\Typeahead;
use mdm\admin\components\Helper;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use kartik\grid\EditableColumn;
use kartik\editable\Editable;
use yii\web\JsExpression;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
    ],

    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        // uncomment below and comment detail if you need to render via ajax
        'detailUrl' => Url::to(['/sales/details']),
//        'detail' => function ($model, $key, $index, $column) {
//            return Yii::$app->controller->renderPartial('_expand-row-details', ['model' => $model]);
//        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],


    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_at',
        'pageSummary' => false,
        'hAlign' => GridView::ALIGN_CENTER,
        'contentOptions' => ['style' => 'width:80px;'],
        'value' => function ($model) {
            if(Yii::$app->controller->id == 'reports'){
                return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
            }
            return DateTimeUtility::getDate($model->created_at, 'h:i A');
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
        'class' => '\kartik\grid\EditableColumn',
        'attribute' => 'transport_name',
        'refreshGrid' => true,
        // Optional: Uncomment if you want to restrict editing conditionally
         'readonly' => function ($model) {
//             return !empty($model->tracking_number);
         },
        'editableOptions' => function ($model, $key, $index) {
            return [
                'header' => 'Transport Info',
                'size' => 'md',
                'formOptions' => [
                    'action' => ['/sales/transport', 'id' => \app\components\Utility::encrypt($model->sales_id)],
                ],
                'beforeInput' => function ($form, $widget) use ($model) {
                    // Prepare dropdown list
                    $dropdown = $form->field($model, 'transport_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(
                            \app\models\Transport::find()->all(),
                            'transport_id',
                            'transport_name'
                        ),
                        [
                            'prompt' => 'Select transport...',
                            'class' => 'form-control',
                        ]
                    );
                    // Prepare tracking number input
                    $tracking = $form->field($model, 'tracking_number')->textInput(['maxlength' => true]);
                    // Return both fields together
                    return $dropdown . $tracking;
                },
                'inputType' => \kartik\editable\Editable::INPUT_HIDDEN, // Required to prevent double render
            ];
        },
        'value' => function ($model) {
            return $model->transport_name
                ? $model->transport_name . "\nTracking ({$model->tracking_number})"
                : 'Set';
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
        'header' => 'Remaining Dues',
        'value' => function ($data) {
            $netPayable = $data->total_amount - $data->discount_amount;
            $adjustedPayment = $data->paid_amount + $data->reconciliation_amount;
            $remainingDue = $netPayable - $adjustedPayment - $data->sales_return_amount;
            return max(0, $remainingDue);
        },
        'format' => ['decimal', 0],
        'hAlign' => GridView::ALIGN_RIGHT,
        'contentOptions' => ['style' => 'width:100px;'],
        'pageSummary' => true,
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'total_amount',
        'hAlign' => GridView::ALIGN_RIGHT,
        'pageSummary' => true,
        'contentOptions' => ['style' => 'width:120px;'],
        'format' => ['decimal', 0],
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
        'headerOptions' => ['style' => 'text-align: center; width:100px;'],
        'contentOptions' => ['style' => 'text-align: center;'],
        'template' => Helper::filterActionColumn('{print} {approved} {update} {delete}'),
        'buttons' => [
            'print' => function ($url, $model) {
                if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_APPROVED) {
                    return ButtonHelper::actionButton('print', Url::to(['sales/print', 'id' => Utility::encrypt($model->sales_id)]), [
                        'title' => Yii::t('app', 'Print Invoice'),
                    ]);
                }
            },
            'delete' => function ($url, $model) {
                if (
                    $model->status != Sales::STATUS_DELETE &&
                    DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y') &&
                    $model->status == Sales::STATUS_APPROVED
                ) {
                    return \app\components\ButtonHelper::actionButton('delete', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure?',
                        'confirmText' => 'Do you really want to delete Invoice #' . $model->sales_id . '?',
                        'confirmButton' => 'Yes, delete it!',
                        'cancelButton' => 'Cancel',
                        'class' => 'btn-confirm',  // ✅ Ensure btn-confirm is here for JS trigger
                        'url' => Url::to(['remove-invoice', 'id' => \app\components\Utility::encrypt($model->sales_id)]),
                        'confirmAjax' => 1,                        // ✅ Enable AJAX call in confirm-buttons.js
                        'pjaxId' => '#salesPjaxGridView',                 // ✅ Optional: PJAX container if you want to reload something
                        'title' => Yii::t('app', 'Delete ' . '# ' . $model->sales_id),
                    ]);
                }
            },
            'approved' => function ($url, $model) {
                if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_PENDING) {
                    return ButtonHelper::actionButton('approve', Url::to(['sales/view', 'id' => Utility::encrypt($model->sales_id)]), [
                        'class' => 'approvedButton',
                        'title' => Yii::t('app', 'Approve ' .  '# ' . $model->sales_id),
                    ]);
                }
            },
            'update' => function ($url, $model) {
                if ($model->status != Sales::STATUS_PENDING && $model->status != Sales::STATUS_DELETE) {
                    if (
                        (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')
                            && Yii::$app->controller->id != 'reports')
                        || (($model->type == Sales::TYPE_SALES || $model->type == Sales::TYPE_SALES_UPDATE)
                            && Yii::$app->controller->id != 'reports')
                    ) {
                        return ButtonHelper::actionButton('update', Url::to(['sales/update', 'sales_id' => Utility::encrypt($model->sales_id)]), [
                            'title' => Yii::t('app', 'Update Invoice# ' . $model->sales_id . ' Customer: ' . $model->client_name),
                        ]);
                    }
                }
            },
            'product' => function ($url, $model) {
                return ButtonHelper::actionButton('details', '#', [
                    'value' => Url::to(['sales-details/details', 'id' => Utility::encrypt($model->sales_id)]),
                    'title' => Yii::t('app', 'Item Details.'),
                ]);
            },
            'payment' => function ($url, $model) {
                if ($model->status != Sales::STATUS_DELETE) {
                    return ButtonHelper::actionButton('payment', '#', [
                        'value' => Url::to(['customer-account/details', 'id' => Utility::encrypt($model->sales_id)]),
                        'title' => Yii::t('app', 'Payment Details'),
                    ]);
                }
            },
            'notification' => function ($url, $model) {
                if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_APPROVED) {
                    return ButtonHelper::actionButton('notification', '#', [
                        'value' => Url::to(['sales/notification', 'id' => Utility::encrypt($model->sales_id)]),
                        'title' => Yii::t('app', 'Email/SMS Notification'),
                    ]);
                }
            },
        ]
    ]
];