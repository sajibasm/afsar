<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <?php Utility::gridViewModal($this, $searchModel); ?>

    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'item_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],


        [
            'attribute' => 'product_status',
            'format' => 'raw',
            'value' => function ($model) {
                return \app\components\BadgeHelper::render($model->product_status);
            },
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'hiddenFromExport' => true,
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="fas fa-pen"></span>', Url::to(['item/update', 'id' => Utility::encrypt($model->item_id)]), [
                        'class' => 'btn btn-warning btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Type'),
                    ]);
                }
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'itemAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, 3, 'item');
    yii\widgets\Pjax::end();
    ?>

</div>
