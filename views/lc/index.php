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

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

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
            'template'=>'{update} ',
            'buttons' => [
                'update' => function ($url, $model) {
                    $class = 'btn btn-info btn-xs';
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->lc_id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update# '.$model->lc_name),
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

    $button = 'New LC';

    yii\widgets\Pjax::begin(['id'=>'lcAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'lc');
    yii\widgets\Pjax::end();
    ?>

</div>
