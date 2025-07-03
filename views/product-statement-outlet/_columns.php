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
            // Normalize old values to new ones
            $type = $model->type;
            if ($type === 'Stock-Received') {
                $type = 'Received';
            } elseif ($type === 'Stock-Store-Transfer') {
                $type = 'Transfer';
            }

            // Return colored badge based on type
            switch ($type) {
                case 'Received':
                    return '<span class="badge" style="background-color: #28a745; color: #fff;">Received</span>';
                case 'Transfer':
                    return '<span class="badge" style="background-color: #17a2b8; color: #fff;">Transfer</span>';
                case 'Sales':
                    return '<span class="badge" style="background-color: #007bff; color: #fff;">Sales</span>';
                case 'Sales-Update':
                    return '<span class="badge" style="background-color: #6f42c1; color: #fff;">Sales Update</span>';
                case 'Sales-Return':
                    return '<span class="badge" style="background-color: #ffc107; color: #212529;">Sales Return</span>';
                case 'Sales-Delete':
                    return '<span class="badge" style="background-color: #dc3545; color: #fff;">Sales Delete</span>';
                default:
                    return '<span class="badge badge-secondary">' . ucfirst($type) . '</span>';
            }
        },
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
];   
