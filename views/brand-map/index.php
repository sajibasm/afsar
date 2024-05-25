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

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

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
            'template'=>'{update} ',
            'hidden'=>Yii::$app->controller->id=='reports'?true:false,
            'buttons' => [
                'update' => function ($url, $model) {
                    $class = 'btn btn-info btn-xs';
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update LC Payment# '.$model->name),
                    ]);
                }
            ]
        ],

    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 3;
    }else{
        $colspan = 4;
    }

    $button = 'Add Brand';

    yii\widgets\Pjax::begin(['id'=>'brandNew']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'Brand-List-'.date('Y-m-d:h:i:s'));
    yii\widgets\Pjax::end();
    ?>



    <?php Pjax::end(); ?>
</div>
