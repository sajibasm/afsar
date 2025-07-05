<?php

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BrandMapSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Brand Maps');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="brand-map-index">

<?php Pjax::begin(); ?>

    <?php

    $gridColumns = [

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'ID',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return $model->id;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Brand',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return $model->name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Status',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return $model->status;
            }
        ],

        [
            'class'=>'kartik\grid\ActionColumn',
            //'hidden'=>true,
            'vAlign'=>GridView::ALIGN_RIGHT,
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'hidden'=>Yii::$app->controller->id=='reports'?true:false,
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

    if(Yii::$app->controller->id=='report'){
        $colspan = 3;
    }else{
        $colspan = 4;
    }

    yii\widgets\Pjax::begin(['id'=>'brandNew']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, 'Brand-map-'.date('Y-m-d:h:i:s'));
    yii\widgets\Pjax::end();
    ?>



    <?php Pjax::end(); ?>
</div>
