<?php

use mdm\admin\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'product_statement_outlet_id',
//    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'outlet_id',
        'label' => 'Outlet',
        'value' => function($model) {
            return ($model->outlet_id) ? $model->outletDetail->name : '';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'item_id',
        'label' => 'Item',
        'value' => function($model) {
            return ($model->item_id) ? $model->itemDetail->item_name : '';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'brand_id',
        'label' => 'Brand',
        'value' => function($model) {
            return ($model->brand_id) ? $model->brandDetail->brand_name : '';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'size_id',
        'label' => 'Size',
        'value' => function($model) {
            return ($model->size_id) ? $model->sizeDetail->size_name : '';
        }
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'quantity',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'type',
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'remarks',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'reference_id',
         'header'=>'Ref'
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'user_id',
         'header'=>'User',
         'value' => function($model) {
             return $model->userDetail->username;
         }
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'template' => Helper::filterActionColumn('{view}'),
        'buttons'=>[
            'view' => function ($url) {
                return Html::a(
                    '<span class="fas fa-eye"></span>',
                    $url,
                    [
                        'title' => 'View',
                        'class' => 'btn btn-default btn-xs',
                        'target' => '_blank',
                        'data-pjax'=>0
                        //'data-toggle' => 'tooltip'
                    ]
                );
            },
        ],
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
    ],

];   
