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
     'attribute'=>'created_at',
     ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'user_id',
        'value' => function($model) {
            return ($model->userDetail) ? $model->userDetail->username : '';
        }
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'outlet_id',
        'value' => function($model) {
            return ($model->outlet_id) ? $model->outletDetail->name : '';
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        'format' => 'raw',
        'value' => function ($model) {
            // Normalize old values
            $type = $model->type;
            if ($type === 'Stock-Received') {
                $type = 'received';
            } elseif ($type === 'Stock-Store-Transfer') {
                $type = 'transfer';
            } else {
                $type = strtolower($type); // To ensure matching keys like 'sales-update'
            }

            return \app\components\BadgeHelper::render($type);
        },
    ],

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

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'remarks',
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


];   
