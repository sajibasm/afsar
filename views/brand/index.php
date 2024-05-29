<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Brand');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-index">
<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>



    <?php
    $button = 'New Brand';
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'item.item_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'brand_name',
            'hAlign' => GridView::ALIGN_CENTER,
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'brand_status',
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
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['brand/update', 'id' => Utility::encrypt($model->brand_id)]), [
                        'class' => 'btn btn-info btn-xs',
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Type'),
                    ]);
                }
            ],
        ],
    ];

    yii\widgets\Pjax::begin(['id' => 'brandAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, 3, 'brand');
    yii\widgets\Pjax::end();
    ?>

</div>
