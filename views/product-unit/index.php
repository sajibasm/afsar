<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductUnitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Product Units');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

<div class="product-unit-index">

    <?php
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'status',
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
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update# '.$model->name),
                    ]);
                }
            ]
        ],
    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 4;
    }else{
        $colspan = 5;
    }

    $button = 'New Product Unit';
    yii\widgets\Pjax::begin(['id'=>'UnitAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'productUnit');
    yii\widgets\Pjax::end();
    ?>

</div>

