<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\SizeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Sizes');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php

    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="size-index">

    <?php

    $gridColumns = [

        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],

        [
            'header'=>'Image',
            'format' => 'html',
            'value' => function($data) { return Html::img($data->getImageUrl(), ['width'=>'100px']); },
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'item.item_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'brand.brand_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'size_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'productUnit.name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'unit_quantity',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'lowest_price',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'size_status',
            'hAlign'=>GridView::ALIGN_CENTER,
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
                    $class = 'btn btn-info btn-xs';
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->size_id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update# '.$model->size_name),
                    ]);
                }
            ]
        ],

    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 9;
    }else{
        $colspan = 10;
    }

    $button = 'New Size';

    yii\widgets\Pjax::begin(['id'=>'sizeAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'size');
    yii\widgets\Pjax::end();
    ?>

</div>
