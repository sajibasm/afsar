<?php
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'header'=>'SL'
    ],
    [
        'attribute'=>'StockId',
        'value'=>function ($model, $key, $index, $widget) {
            return $model->product_stock_id;
        },
        'group'=>false,  // enable grouping
    ],

    [
        'attribute'=>'created_at',
        'value'=>function($model){
            return $model->productStock->created_at;
        },
        'group'=>false,  //enable grouping
        'subGroupOf'=>0, // StockId column index is the parent group,
    ],

    [
        'attribute' => 'invoice_no',
        'hiddenFromExport'=>true,
        'value'=>function($model){
            return $model->productStock->invoice_no;
        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],

    [
        'hiddenFromExport'=>true,
        'attribute' => 'type',
        'value'=>function($model){
            return $model->productStock->type;
        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],

    [
        'attribute'=>'user_id',
        'hiddenFromExport'=>true,
        'value'=>function($model){
            return $model->productStock->user->username;
        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],

    [
        'header'=>'Warehouse',
        'attribute' => 'Warehouse',
        'value'=>function ($model, $key, $index, $widget) {
            if(isset($model->productStock->warehouse->warehouse_name)){
                return $model->productStock->warehouse->warehouse_name;
            }else{
                return "";
            }
        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],
    [
        'header'=>'LC',
        'value'=>function($model){

            if(isset($model->productStock->lc->lc_name)){
                return $model->productStock->lc->lc_name;
            }else{
                return "";
            }

        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],
    [
        'header'=>'Supplier',
        'value'=>function($model){
            if(isset($model->productStock->supplier->name)){
                return $model->productStock->supplier->name;
            }else {
                return "";
            }
        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],
    [
        'header'=>'Remarks',
        'hiddenFromExport'=>true,
        'value'=>function($model){
            return $model->productStock->remarks;
        },
        'group'=>false,  // enable grouping
        'subGroupOf'=>0 // StockId column index is the parent group,
    ],
    [
        'attribute' => 'item',
        'value'=>function ($model, $key, $index, $widget) {
            return $model->item->item_name;
        },
    ],
    [
        'attribute' => 'brand',
        'value'=>function ($model, $key, $index, $widget) {
            return $model->brand->brand_name;
        }
    ],
    [
        'attribute' => 'size',
        'value'=>function ($model, $key, $index, $widget) {
            return $model->size->size_name;
        }
    ],
    [
        'header'=>'Cost',
        'attribute' => 'cost_price',
        'hAlign'=>'right',
        'format'=>['decimal', 0],
        'value'=>function ($model, $key, $index, $widget) {
            return $model->cost_price;
        },


    ],
    [
        'header'=>'Wholesale',
        'attribute' => 'wholesale_price',
        'hAlign'=>'right',
        'format'=>['decimal', 0],
        'value'=>function ($model, $key, $index, $widget) {
            return $model->wholesale_price;
        },
    ],
    [
        'header'=>'Retail',
        'attribute' => 'retail_price',
        'hAlign'=>'right',
        'format'=>['decimal', 0],
        'value'=>function ($model, $key, $index, $widget) {
            return $model->retail_price;
        },
    ],
    [
        'header'=>'Old Qty',
        'attribute' => 'previous_quantity',
        'hAlign'=>'right',
        'format'=>['decimal', 0],
        'value'=>function ($model, $key, $index, $widget) {
            return $model->previous_quantity;
        },
    ],
    [
        'header'=>'New Qty',
        'attribute' => 'new_quantity',
        'hAlign'=>'right',
        'format'=>['decimal', 0],
        'value'=>function ($model, $key, $index, $widget) {
            return $model->new_quantity;
        },
    ],
    [
        'header'=>'Total Qty',
        'attribute' => 'total_quantity',
        'hAlign'=>'right',
        'format'=>['decimal', 0],
        'value'=>function ($model, $key, $index, $widget) {
            return $model->total_quantity;
        },
    ],
//    [
//        'class' => '\kartik\grid\ActionColumn',
//        'hidden'=>true,
//        'hiddenFromExport'=>true,
//        'header'=>'Action',
//        'template'=>'',
//        'buttons' => [
//            'update' => function ($url, $model) {
//                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['stock-update','id'=>$model->product_stock_id]), [
//                    'class'=>'btn btn-primary btn-xs',
//                    'data-pjax'=>0,
//                    'title' => Yii::t('app', 'Update Stock# '.$model->product_stock_id),
//                ]);
//            },
//
//            //                'details' => function ($url, $model) {
//            //                    return Html::button('<span class="glyphicon glyphicon-list"></span>', [
//            //                        'class'=>'btn btn-success btn-xs modalUpdateBtn',
//            //                        'title' => Yii::t('app', 'Product List '),
//            //                        'data-pjax'=>1,
//            //                        'value' =>Url::to(['product-stock-items/details','id'=>$model->product_stock_id])
//            //                    ]);
//            //                },
//        ],
//
//    ],


];