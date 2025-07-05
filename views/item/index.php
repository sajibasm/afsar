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
            'headerOptions' => ['style' => 'text-align: center; width:50px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'urlCreator' => function ($action, $model, $key, $index) {
                return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
            },
            'template' => \mdm\admin\components\Helper::filterActionColumn('{update}'),
            'buttons' => [
                'update' => function ($url, $model) {
                    return \app\components\ButtonHelper::actionButton('update', $url, [
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update'),
                    ]);
                },
            ]
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'itemAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, 4, 'item');
    yii\widgets\Pjax::end();
    ?>

</div>
