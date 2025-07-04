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

<div class="sales-draft-index">
    <?php
    Modal::begin([
        'options' => [
            'id' => 'modal',
            'tabindex' => false,
        ],
        'clientOptions'=>[
            'backdrop' => 'static',
            'keyboard' => false,
        ],
        'header' => "<b style='margin:0; padding:0;'> Details </b>",
        'closeButton' => ['id' => 'close-button'],
        'size'=>Modal::SIZE_DEFAULT

    ]);
    echo '<div id="modalContent"></div>';
    Modal::end();
    ?>


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
                'header'=>'Qty',
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
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                //'template'=>'{delete}',
                'template'=>'{delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::button('<span class="fas fa-pen"></span>', [
                            'class'=>'btn btn-warning btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', $model->item->item_name.' Update'),
                            'id'=>'modalUpdateBtn1',
                            'data-pjax'=>1,
                            'value' =>Url::to(['sales/draft-update','id'=> Utility::encrypt($model->sales_details_id)])
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('delete', '#', [
                            'confirm' => true,
                            'confirmTitle' => 'Are you sure?',
                            'confirmText' => 'Do you really want to delete this item?',
                            'confirmButton' => 'Yes, delete it!',
                            'cancelButton' => 'Cancel',
                            'class' => 'btn-confirm',
                            'data-pjax' => 0,
                            'value' => null,
                            // ✅ Use camelCase keys here:
                            'confirmAjax' => 1,             // Not 'confirm-ajax'
                            'pjaxId' => '#sell',            // Not 'pjax-id'
                            'url' => Url::to(['/sales/invoice-item-delete', 'id' => \app\components\Utility::encrypt($model->sales_details_id)]),
                        ]);
                    },

                ],
            ]

        ],
    ]); ?>
</div>