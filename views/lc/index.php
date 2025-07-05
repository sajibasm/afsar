<?php


/* @var $this yii\web\View */
/* @var $searchModel app\models\LcSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'LC');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="lc-index">
    <?php
    $gridColumns = [

        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'lc_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'lc_number',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'branch',
            'header'=>'Bank(Branch)',
            'value'=>function($data){
                return $data->branch->bank->bank_name.' - '.$data->branch->branch_name;
            }
        ],

        [
            'class'=>'kartik\grid\ActionColumn',
            //'hidden'=>true,
            'vAlign'=>GridView::ALIGN_RIGHT,
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
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
        $colspan = 4;
    }else{
        $colspan = 5;
    }

    yii\widgets\Pjax::begin(['id'=>'lcAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, 'lc');
    yii\widgets\Pjax::end();
    ?>

</div>
