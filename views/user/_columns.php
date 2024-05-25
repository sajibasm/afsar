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
//        [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'user_id',
//    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'first_name',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'last_name',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'username',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'email',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'password_hash',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'auth_key',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'password_reset_token',
    // ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'user_image',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_at',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'updated_at',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{view}{update}{outlet}{permission}',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [

            'view' => function ($url) {
                return Html::a(
                    '<i class="material-icons" style="font-size:16px">pageview</i>',
                    $url,
                    [
                        'title' => 'View',
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn-info btn-xs',
                    ]
                );
            },

            'update' => function ($url) {
                return Html::a(
                    '<i class="material-icons" style="font-size:16px">system_update_alt</i>',
                    $url,
                    [
                        'title' => 'Assign To Outlet',
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-warning btn-xs',
                    ]
                );
            },


            'outlet' => function ($url) {
                return Html::a(
                    '<i class="material-icons" style="font-size:16px">group_add</i>',
                    $url,
                    [
                        'title' => 'outlet',
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-primary btn-xs',
                    ]
                );
            },
            'permission' => function ($url, $model) {
                return Html::a(
                    '<i class="material-icons" style="font-size:16px">settings_applications</i>',
                    ['/asm/module-permission/assign', 'user' => Utility::encrypt($model->user_id)],
                    [
                        'title' => 'Permission',
                        'data-pjax' => 0,
                        'target' => '_blank',
                        'class' => 'btn btn-success btn-xs',
                    ]
                );
            },
        ],
        //'outletOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip'],
        'deleteOptions' => ['role' => 'modal-remote', 'title' => 'Delete',
            'data-confirm' => false, 'data-method' => false,// for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Are you sure?',
            'data-confirm-message' => 'Are you sure want to delete this item'],
    ],

];   