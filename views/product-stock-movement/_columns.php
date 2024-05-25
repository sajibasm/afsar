<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;

return [
//    [
//        'class' => 'kartik\grid\CheckboxColumn',
//        'width' => '20px',
//    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'product_stock_outlet_code',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'invoice',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'note',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'remarks',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'params',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'transferOutlet',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'receivedOutlet',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'transferBy',
     ],

     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'receivedBy',
     ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'createdAt',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updatedAt',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'template'=>'{details} {view} {approve} {reject}',
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>Utility::encrypt($key)]);
        },
        'buttons'=>[

            'details' => function ($url) {
                return \yii\helpers\Html::a(
                    '<span class="glyphicon glyphicon-th"></span>',
                    $url,
                    [
                        'title' => 'Items',
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip'
                    ]
                );
            },

            'approve' => function ($url, $model) {

                if($model->status==='pending') {
                    return \yii\helpers\Html::a(
                        '<span class="glyphicon glyphicon-ok"></span>',
                        $url,
                        [
                            'title' => 'Approve',
                            'role' => 'modal-remote',
                            'data-toggle' => 'tooltip'
                        ]
                    );
                }
            },

            'reject' => function ($url, $model) {
                if($model->status==='pending') {
                    return \yii\helpers\Html::a(
                        '<span class="glyphicon glyphicon-remove"></span>',
                        $url,
                        [
                            'title' => 'Reject',
                            'role' => 'modal-remote',
                            'data-toggle' => 'tooltip'
                        ]
                    );
                }
            },
        ],

        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
    ],

];   
