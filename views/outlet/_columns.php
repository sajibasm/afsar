<?php

use kartik\helpers\Html;
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
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($model) {
            if ($model->status == 1) {
                return \app\components\BadgeHelper::render('Active');
            } elseif ($model->status == 0) {
                return \app\components\BadgeHelper::render('Inactive');
            } else {
                return \app\components\BadgeHelper::render('Unknown');
            }
        },
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'template' => ' {update}',  // Only view and custom update
        'headerOptions' => ['style' => 'text-align: center; width:50px;'],
        'contentOptions' => ['style' => 'text-align: center;'],
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
        },
        'buttons' => [
            'update' => function ($url, $model) {
                return \app\components\ButtonHelper::actionButton('update', $url, [
                    'data-pjax' => 0,
                    'title' => Yii::t('app', 'Update'),
                ]);
            },
        ],
    ],


];   