<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\ProductStock;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product Stock');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'stock_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>


<div class="product-stock-index">


    <?php
    $gridColumns = [
        /*        [
                    'class' => 'kartik\grid\SerialColumn',
                    'header'=>'Serial'
                ],*/
        [
            'header' => 'StockId',
            'attribute' => 'product_stock_id',
        ],
//        [
//            'attribute' => 'invoice_no',
//        ],

        [
            'header' => 'Date',
            'attribute' => 'created_at',
        ],

        [
            'header' => 'Type',
            'hiddenFromExport' => true,
            'attribute' => 'type',
        ],

        [
            'header' => 'Received|Source ',
            'hiddenFromExport' => true,
            'value' => function ($model) {
                if ($model->type === ProductStock::TYPE_TRANSFER) {
                    $params = Json::decode($model->params);
                    if (isset($params['receivedOutlet'])) {
                        return $params['receivedOutlet'];
                    } else {
                        if (isset($params['outlet'])) {
                            return $params['outlet'];
                        } else {
                            //print_r($model);
                            //die();
                        }
                    }
                } else if ($model->type === ProductStock::TYPE_RECEIVED) {
                    $params = Json::decode($model->params);
                    return $params['transferOutlet'];
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
            'header' => 'Warehouse',
            'attribute' => 'Warehouse',
            'value' => function ($model, $key, $index, $widget) {
                return $model->warehouse ? $model->warehouse->warehouse_name : "";
            },
        ],
        [
            'header' => 'LC',
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
            'header' => 'Status',
            'hiddenFromExport' => true,
            'attribute' => 'status',
        ],

        [
            'class' => '\kartik\grid\ActionColumn',
            'hiddenFromExport' => true,
            'header' => 'Action',
            'template' => '{approved} {update} {details} {product-transfer} {invoice}',
            'buttons' => [


                'invoice' => function ($url, $model) {
                    //if(Helper::checkRoute('print')){
                    if ($model->status !== ProductStock::STATUS_REJECT) {
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['product-stock/print', 'id' => Utility::encrypt($model->product_stock_id)]), [
                            'class' => 'btn btn-default btn-xs',
                            'title' => Yii::t('app', 'Print'),
                            'data-pjax' => 0,
                            'target' => '_blank'
                        ]);
                    }
                    //}
                },

                'approved' => function ($url, $model) {

                    if ($model->status === ProductStock::STATUS_PENDING && $model->type === ProductStock::TYPE_RECEIVED) {
                        return Html::a('<span class="fa fa-check"></span>', Url::to(['product-stock/received-view', 'id' => Utility::encrypt($model->product_stock_id)]), [
                            'class' => 'btn btn-default btn-xs approvedButton',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->product_stock_id),
                        ]);
                    }

                },

                'update' => function ($url, $model) {

                    if ($model->type === ProductStock::TYPE_IMPORT && $model->status === ProductStock::STATUS_ACTIVE && empty($model->params)) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['product-stock/stock-update', 'id' => $model->product_stock_id]), [
                            'class' => 'btn btn-primary btn-xs',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Update Stock# ' . $model->product_stock_id),
                        ]);
                    }
                },

                'details' => function ($url, $model) {
                    return Html::button('<span class="glyphicon glyphicon-list"></span>', [
                        'class' => 'btn btn-success btn-xs modalUpdateBtn',
                        'title' => Yii::t('app', 'Product List '),
                        'data-pjax' => 1,
                        'value' => Url::to(['product-stock/items-details', 'id' => Utility::encrypt($model->product_stock_id)])
                    ]);
                },

                'product-transfer' => function ($url, $model) {
                    if (($model->type === ProductStock::TYPE_IMPORT || $model->type === ProductStock::TYPE_MIGRATION) && $model->status === ProductStock::STATUS_ACTIVE && empty($model->params)) {
                        return Html::a('<span class="glyphicon glyphicon-arrow-right"></span>', Url::to(['transfer-to-outlet', 'id' => Utility::encrypt($model->product_stock_id)]), [
                            'class' => 'btn btn-default btn-xs',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Transfer to Outlet# ' . $model->product_stock_id),
                        ]);
                    }
                },
            ],
        ],
    ];

    $title = 'Stock Statement';
    if (Yii::$app->controller->id == 'report') {
        $colspan = 16;
    } else {
        $colspan = 17;
    }

    $button = [];
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>


</div>
