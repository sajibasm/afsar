<?php

use app\components\Utility;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="sales-draft-update-removed">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'item_id',
                'header'=>'Item',
                'value'=>function($data){
                    return $data->item->item_name;
                }
            ],

            [
                'attribute'=>'brand_id',
                'header'=>'Brand',
                'value'=>function($data){
                    return $data->brand->brand_name;
                }
            ],
            [
                'attribute'=>'size_id',
                'header'=>'Size',
                'value'=>function($data){
                    return $data->size->size_name;
                }
            ],
            [
                'attribute'=>'sales_amount',
                'header'=>'Unit Price',
                'value'=>function($data){
                    return $data->sales_amount.' '.Yii::$app->params['currency'];
                }
            ],
            [
                'attribute'=>'quantity',
                'header'=>'Quantity',
                'value'=>function($data){
                    return $data->quantity;
                }
            ],
            [
                'attribute'=>'total_amount',
                'header'=>'Total',
                'value'=>function($data){
                    return $data->total_amount.' '.Yii::$app->params['currency'];
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Action',
                'template'=>'{restore}',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'buttons' => [
                    'restore' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('restore', '#', [
                            'confirm' => true,
                            'confirmTitle' => 'Are you sure?',
                            'confirmText' => 'Do you really want to restore this item?',
                            'confirmButton' => 'Yes, restore it!',
                            'cancelButton' => 'Cancel',
                            'class' => 'btn-confirm',  // Required for SweetAlert & JS
                            'data-pjax' => 0,
                            'value' => null,
                            'url' => Url::to(['/sales/invoice-item-update-restore', 'id' => \app\components\Utility::encrypt($model->sales_details_id)]),
                            'confirmAjax' => 1,         // Required for AJAX trigger
                            'pjaxId' => '#sellUpdate',  // Optional PJAX container
                        ]);
                    },
                ],
            ]

        ],
    ]); ?>
</div>