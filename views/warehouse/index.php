<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\WarehouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Warehouse');
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

<div class="warehouse-index">

    <?php
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'warehouse_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'cities.city_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'address1',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'address2',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'postal_code',
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
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->warehouse_id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update# '.$model->warehouse_name),
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

    $button = 'New Warehouse';
    yii\widgets\Pjax::begin(['id'=>'warehouseAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'warehouse');
    yii\widgets\Pjax::end();
    ?>

</div>
