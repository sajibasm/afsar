<?php

use app\components\ConstrainUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
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
        'header' => 'Outlet',
        'attribute' => 'user.userOutletDetail',
        'value' => function ($model) {
            $outlet = '';
            foreach ($model->userOutletDetail as $outletModel) {
                $outlet .= $outletModel->outletDetail->name.', ';
            }
            return trim($outlet, ', ');
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'email',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'user_image',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value' => function ($model) {
            return ConstrainUtility::USER_STATUS_LIST[$model->status];
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_at',
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        //'hidden'=>true,
        'hiddenFromExport'=>true,
        'hAlign'=>GridView::ALIGN_CENTER,
        'template'=>'{outlet} {update}',
        'buttons' => [

            'update' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->user_id)],
                    [
                        'class'=>'btn btn-info btn-xs',
                        'data-ajax'=>0,
                        'data-toggle'=>'tooltip',
                        'title'=>'Update '.$model->username,
                    ]
                );
            },
            'outlet' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['outlet', 'id'=> Utility::encrypt($model->user_id)],
                    [
                        'class'=>'btn btn-warning btn-xs',
                        'data-ajax'=>0,
                        'data-toggle'=>'tooltip',
                        'title'=>'Outlet Assign '.$model->username,
                    ]
                );
            },

//            'update' => function ($url, $model) {
//                    return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->id)]),[
//                        'class'=>'btn btn-default btn-xs approvedButton',
//                        'data-pjax'=>0,
//                        'title' => Yii::t('app', 'User Info'),
//                    ]);
//            },
        ],

    ],

];   