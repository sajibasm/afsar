<?php
use app\components\Utility;
use app\models\ProductStock;
use mdm\admin\components\Helper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

return [
    [
        'attribute' => 'product_stock_id',
    ],
    [
        'attribute' => 'created_at',
    ],

    [
        'hiddenFromExport' => true,
        'attribute' => 'type',
    ],

    [
        'attribute' => 'invoice_no',
    ],

    [
        'header' => 'Received | Source',
        'hiddenFromExport' => true,
        'format' => 'raw',
        'value' => function ($model) {
            $params = [];

            // Decode JSON safely
            if (!empty($model->params)) {
                $decoded = Json::decode($model->params, true);
                if (is_array($decoded)) {
                    $params = $decoded;
                }
            }

            // Logic based on type
            if ($model->type === ProductStock::TYPE_TRANSFER) {
                return $params['receivedOutlet'] ?? ($params['outlet'] ?? '');
            } elseif ($model->type === ProductStock::TYPE_RECEIVED) {
                return $params['transferOutlet'] ?? '';
            }

            return '';
        },
    ],


    [
        'header' => 'Ref',
        'hiddenFromExport' => true,
        'value' => function ($model) {
            if ($model->type === ProductStock::TYPE_TRANSFER) {
                $params = Json::decode($model->params);
                if (isset($params['outletStock'])) {
                    return $params['outletStock'];
                } else {
                    if (isset($params['refId'])) {
                        return $params['refId'];
                    }
                }
            } else if ($model->type === ProductStock::TYPE_RECEIVED) {
                $params = Json::decode($model->params);
                return $params['ref'];

            }
            return '';
        },
    ],

    [
        'attribute' => 'Warehouse',
        'value' => function ($model, $key, $index, $widget) {
            return $model->warehouse ? $model->warehouse->warehouse_name : "";
        },
    ],
    [
        'attribute' => 'lc_id',
        'value' => function ($model) {
            return $model->lc ? $model->lc->lc_name : "";
        },
    ],
    [
        'header' => 'Supplier',
        'value' => function ($model) {
            return $model->supplier ? $model->supplier->name : "";
        },
    ],


    [
        'attribute' => 'user_id',
        'value' => function ($model) {
            return $model->user ? $model->user->username : "";
        },
    ],


    [
        'header' => 'Status',
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
        'class' => '\kartik\grid\ActionColumn',
        'hiddenFromExport' => true,
        'header' => 'Action',
        'template' => Helper::filterActionColumn('{approved} {update} {details} {product-transfer} {invoice}'),
        'buttons' => [
            'invoice' => function ($url, $model) {
                    return Html::a('<span class="fas fa-print"></span>', Url::to(['product-stock/print', 'id' => Utility::encrypt($model->product_stock_id)]), [
                        'class' => 'btn btn-default btn-xs',
                        'title' => Yii::t('app', 'Print'),
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
            },

            'approved' => function ($url, $model) {
                if ($model->status === ProductStock::STATUS_PENDING && $model->type === ProductStock::TYPE_RECEIVED) {
                    return Html::a('<span class="fas fa-check"></span>', Url::to(['product-stock/received-view', 'id' => Utility::encrypt($model->product_stock_id)]), [
                        'class' => 'btn btn-default btn-xs approvedButton',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->product_stock_id),
                    ]);
                }

            },
            'update' => function ($url, $model) {
                if ($model->type === ProductStock::TYPE_IMPORT && $model->status === ProductStock::STATUS_ACTIVE && empty($model->params)) {
                    return Html::a('<span class="fas fa-pen"></span>', Url::to(['product-stock/stock-update', 'id' => $model->product_stock_id]), [
                        'class' => 'btn btn-primary btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Stock# ' . $model->product_stock_id),
                    ]);
                }
            },

            'details' => function ($url, $model) {
                return Html::button('<span class="fas fa-list"></span>', [
                    'class' => 'btn btn-success btn-xs modalUpdateBtn',
                    'title' => Yii::t('app', 'Product List '),
                    'data-pjax' => 1,
                    'value' => Url::to(['product-stock/items-details', 'id' => Utility::encrypt($model->product_stock_id)])
                ]);
            },

            'product-transfer' => function ($url, $model) {
                if (($model->type === ProductStock::TYPE_IMPORT || $model->type === ProductStock::TYPE_MIGRATION) && $model->status === ProductStock::STATUS_ACTIVE && empty($model->params)) {
                    return Html::a('<span class="fas fa-truck-loading"></span>', Url::to(['transfer-to-store', 'id' => Utility::encrypt($model->product_stock_id)]), [
                        'class' => 'btn btn-default btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Transfer to Store# ' . $model->product_stock_id),
                    ]);
                }
            },
        ],
    ],
];