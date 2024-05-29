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
    Utility::getMessage();

?>


<div class="box box-info">

    <?php
    $button = 'New Reconciliation Type';
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
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'status',
            'hAlign' => GridView::ALIGN_CENTER,
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'hiddenFromExport' => true,
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['reconciliation-type/update', 'id' => Utility::encrypt($model->id)]), [
                        'class' => 'btn btn-info btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Type'),
                    ]);
                }
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'expenseAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, 4, 'reconsiliation-type');
    yii\widgets\Pjax::end();
    ?>
</div>




