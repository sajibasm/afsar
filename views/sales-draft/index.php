<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Items Stuck');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-draft-index">

    <?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
    ?>

    <?php
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header'=>'Invoice',
            'attribute' => 'sales_id',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'type',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return \app\models\SalesDraft::typeLabel($model->type);
            }
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'header'=>'Invoice',
            'attribute' => 'item_id',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>'item.item_name'
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign'=>GridView::ALIGN_CENTER,
            'attribute'=>'brand',
            'value'=>'brand.brand_name'
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign'=>GridView::ALIGN_CENTER,
            'attribute'=>'size_id',
            'value'=>'size.size_name'
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign'=>GridView::ALIGN_CENTER,
            'attribute'=>'user_id',
            'value'=>'user.username'
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'hAlign'=>GridView::ALIGN_CENTER,
            'attribute'=>'quantity',
        ],

        [
            'class'=>'kartik\grid\ActionColumn',
            //'hidden'=>true,
            'vAlign'=>GridView::ALIGN_RIGHT,
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'template'=>'{update} ',
            'buttons' => [
                'update' => function ($url, $model) {
                    $class = 'btn btn-warning btn-xs';
                    return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::to(['delete','id'=>Utility::encrypt($model->sales_details_id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update# '.$model->item->item_name),
                    ]);
                }
            ]
        ],
    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 3;
    }else{
        $colspan = 3;
    }

    $button = '';
    yii\widgets\Pjax::begin(['id'=>'InvoiceCardAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'InvoiceCard');
    yii\widgets\Pjax::end();
    ?>

</div>
