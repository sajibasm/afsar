<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceoncliationTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reconciliation Types');
$this->params['breadcrumbs'][] = $this->title;
Utility::gridViewModal($this, $searchModel);
?>


<div class="box box-info">

    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'show_invoice',
            'hAlign' => GridView::ALIGN_CENTER,
        ],
        [
            'attribute' => 'type',
            'format' => 'raw',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return \app\components\BadgeHelper::render($model->status);
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
                    return Html::a('<span class="fas fa-pen"></span>', Url::to(['reconciliation-type/update', 'id' => Utility::encrypt($model->id)]), [
                        'class' => 'btn btn-warning btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Type'),
                    ]);
                }
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'expenseAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, 4, 'reconsiliation-type');
    yii\widgets\Pjax::end();
    ?>
</div>




