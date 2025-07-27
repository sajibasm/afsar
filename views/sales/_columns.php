<?php

use app\components\ButtonHelper;
use app\components\DateTimeUtility;
use app\components\SystemSettings;
use app\components\Utility;
use app\models\Sales;
use app\models\Transport;
use kartik\editable\Editable;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


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
        'contentOptions' => ['style' => 'width:100px;'],
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
        'format' => 'raw',
        'pageSummary' => false,
        //'noWrap' => true,
        'hAlign' => GridView::ALIGN_CENTER,
        'contentOptions' => ['style' => 'width:150px;'],
        'value' => function ($model) {
            return $model->clientContactInfo;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'payment_condition',
        'format' => 'text',
        'hAlign' => GridView::ALIGN_CENTER,
        'contentOptions' => ['style' => 'width:180px; white-space: nowrap;'],
        'value' => function ($model) {
            return $model->getPaymentConditionLabel(); // assuming you have a method returning readable text
        },
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'payment_due_date',
        'format' => 'raw',
        'hAlign' => GridView::ALIGN_CENTER,
        'contentOptions' => ['style' => 'width:200px; white-space: nowrap;'],
        'value' => function ($model) {
            if (!$model->payment_due_date) {
                return '<span style="color:gray;">N/A</span>';
            }

            $dueDate = new \DateTime($model->payment_due_date);
            $today = new \DateTime(date('Y-m-d'));
            $dateFormatted = Yii::$app->formatter->asDate($dueDate, 'php:Y-m-d');
            $diff = $today->diff($dueDate)->days;

            if ($dueDate < $today) {
                return "<div>{$dateFormatted}<br><span style='color: #ff6b6b; font-weight: bold;'>Overdue ({$diff} days ago)</span></div>";
            } elseif ($dueDate > $today) {
                return "<div>{$dateFormatted}<br><span style='color: #38a169; font-weight: bold;'>Due in {$diff} days</span></div>";
            } else {
                return "<div>{$dateFormatted}<br><span style='color: #e67e22; font-weight: bold;'>Due Today</span></div>";
            }
        },
    ],

    [
        'class' => '\kartik\grid\EditableColumn',
        'contentOptions' => ['style' => 'width:200px;'],
        'attribute' => 'transport_name',
        'refreshGrid' => true, // ✅ Triggers grid refresh if pjaxContainerId is properly set

        'readonly' => function ($model) {
            // Only allow editing when status is APPROVED
            return $model->status !== Sales::STATUS_APPROVED;
        },

        'editableOptions' => function ($model, $key, $index) {
            return [
                'header' => 'Transport Info',
                'size' => 'md',
                'formOptions' => [
                    'action' => ['/sales/transport', 'id' => Utility::encrypt($model->sales_id)],
                ],
                'beforeInput' => function ($form, $widget) use ($model) {
                    $dropdown = $form->field($model, 'transport_id')->dropDownList(
                        ArrayHelper::map(
                            Transport::find()->all(),
                            'transport_id',
                            'transport_name'
                        ),
                        [
                            'prompt' => 'Select transport...',
                            'class' => 'form-control',
                        ]
                    );

                    $tracking = $form->field($model, 'tracking_number')->textInput(['maxlength' => true]);

                    return $dropdown . $tracking;
                },
                'inputType' => Editable::INPUT_HIDDEN, // ❗ Must use INPUT_HIDDEN to allow multiple custom fields
                'pjaxContainerId' => 'salesPjaxGridView', // ✅ Required for proper PJAX refresh
                'asPopover' => true, // default is true, but make sure
                'afterInput' => null,
            ];
        },

        'value' => function ($model) {
            return $model->transport_name
                ? $model->transport_name . "\n - {$model->tracking_number}"
                : ($model->status === Sales::STATUS_DELETE ? '' : 'Set');
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
        'headerOptions' => ['style' => 'text-align: center; width:100px;'],
        'contentOptions' => ['style' => 'text-align: right;'],
        'pageSummary' => true,
        'format' => ['decimal', 2],
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
        'format' => ['decimal', 2],
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
        'format' => ['decimal', 2],
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
        'format' => ['decimal', 2],
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
        'format' => ['decimal', 2],
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
        'format' => ['decimal', 2],
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
        'format' => ['decimal', 2],
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
        'template' => Helper::filterActionColumn('{print} {notification} {update} {delete} {approve}'),
        'buttons' => [

            'notification' => function ($url, $model) {
                if ($model->status == Sales::STATUS_APPROVED) {
                    return ButtonHelper::actionButton('notification', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Send Invoice Email?',
                        'confirmText' => 'Do you want to send this invoice to the customer via email?',
                        'confirmButton' => 'Yes, send it!',
                        'cancelButton' => 'No, cancel',
                        'class' => 'btn-confirm',
                        'url' => Url::to(['/sales/notification']), // Ensure this points to correct action
                        'confirmAjax' => 1,
                        'pjaxId' => '#salesPjaxGridView',
                        'data-id' => Utility::encrypt($model->sales_id),
                        'title' => Yii::t('app', 'Send Invoice'),
                    ]);
                }
                return null;
            },
            'print' => function ($url, $model) {
                if ($model->status != Sales::STATUS_DELETE && $model->status == Sales::STATUS_APPROVED) {
                    return ButtonHelper::actionButton('print', Url::to(['sales/print', 'id' => Utility::encrypt($model->sales_id)]), [
                        'title' => Yii::t('app', 'Print Invoice'),
                    ]);
                }
            },
            'update' => function ($url, $model) {
                if ($model->status == Sales::STATUS_PENDING) {

                    if (
                        (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')
                            && Yii::$app->controller->id != 'reports')
                        || (($model->type == Sales::TYPE_SALES || $model->type == Sales::TYPE_SALES_UPDATE)
                            && Yii::$app->controller->id != 'reports')
                    ) {
                        return ButtonHelper::actionButton('update', Url::to(['update', 'sales_id' => Utility::encrypt($model->sales_id)]), [
                            'title' => Yii::t('app', 'Update Invoice'),
                        ]);
                    }
                }
            },
            'delete' => function ($url, $model) {
                if (
                    ($model->status === Sales::STATUS_PENDING || $model->status === Sales::STATUS_APPROVED) &&
                    DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')
                ) {
                    return ButtonHelper::actionButton('delete', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure?',
                        'confirmText' => 'Do you really want to delete Invoice?',
                        'confirmButton' => 'Yes, delete it!',
                        'cancelButton' => 'Cancel',
                        'class' => 'btn-confirm',  // ✅ Required for JS
                        'url' => Url::to(['delete']),  // ✅ No ID here!
                        'confirmAjax' => 1,         // ✅ Triggers AJAX call in your JS
                        'pjaxId' => '#salesPjaxGridView',
                        'data-id' => Utility::encrypt($model->sales_id),   // ✅ Send ID separately
                        'title' => Yii::t('app', 'Delete'),
                    ]);
                }
            },
            'approve' => function ($url, $model) {
                if ($model->status == Sales::STATUS_PENDING) {

                    return ButtonHelper::actionButton('approve', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure?',
                        'confirmText' => 'Do you want to approve Invoice #' . $model->sales_id . '?',
                        'confirmButton' => 'Yes, approve it!',
                        'cancelButton' => 'Cancel',
                        'class' => 'btn-confirm',  // ✅ Ensure btn-confirm is here for JS trigger
                        'url' => Url::to(['approve']),
                        'data-id' => Utility::encrypt($model->sales_id),   // ✅ Send ID separately
                        'confirmAjax' => 1,                        // ✅ Enable AJAX call in confirm-buttons.js
                        'pjaxId' => '#salesPjaxGridView',                 // ✅ Optional: PJAX container if you want to reload something
                        'title' => Yii::t('app', 'Approve action !'),
                    ]);
                }
            }
        ]
    ]
];