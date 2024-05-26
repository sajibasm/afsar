<?php
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
//        [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'outletId',
//    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'outletCode',
//    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'address1',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'address2',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'logo',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'logoWaterMark',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'contactNumber',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'email',
     ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], 
    ],

];   