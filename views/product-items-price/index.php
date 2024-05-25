<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductItemsPriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t('app', 'Product Items Prices');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName                = 'product_price_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

Utility::gridViewModal($this, $searchModel);
Utility::getMessage();

$gridColumns = [
    [
        'class' => 'kartik\grid\SerialColumn',
        'header' => '#',
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Item',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->item->item_name;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Brand',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->brand->brand_name;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Brand',
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model) {
            return $model->size->size_name;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Cost',
        'format' => ['decimal', 2],
        'hAlign' => GridView::ALIGN_RIGHT,
        'value' => function ($model) {
            return $model->cost_price;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Wholesale',
        'format' => ['decimal', 2],
        'hAlign' => GridView::ALIGN_RIGHT,
        'value' => function ($model) {
            return $model->wholesale_price;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Retail',
        'format' => ['decimal', 2],
        'hAlign' => GridView::ALIGN_RIGHT,
        'value' => function ($model) {
            return $model->retail_price;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Quantity',
        'format' => ['decimal', 2],
        'hAlign' => GridView::ALIGN_RIGHT,
        'value' => function ($model) {
            return $model->quantity;
        }
    ],


    [
        'class' => '\kartik\grid\ActionColumn',
        'header' => 'Action',
        'template' => '{update}',
        'buttons' => [
            'update' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id' => Utility::encrypt($model->product_stock_items_id)], [
                        'class' => 'btn btn-info btn-xs',
                        'data-ajax' => 0,
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('app', "Update " . $model->item->item_name),
                    ]
                );
            }
        ]
    ]
];

if (Yii::$app->controller->id == 'report') {
    $colspan = 8;
} else {
    $colspan = 8;
}

$button = null;

?>

<?php
yii\widgets\Pjax::begin(['id' => 'productPriceAjaxGridView']);
echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
yii\widgets\Pjax::end();
?>


