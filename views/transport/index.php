<?php



/* @var $this yii\web\View */
/* @var $searchModel app\models\TransportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Transports');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

<div class="transport-index">
    <?php
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'transport_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'transport_address',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'transport_contact_person',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'transport_contact_number',
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
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->transport_id)]),[
                        'class'=>$class,
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update# '.$model->transport_name),
                    ]);
                }
            ]
        ],
    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 3;
    }else{
        $colspan = 3;
    }

    $button = 'New Transport';
    yii\widgets\Pjax::begin(['id'=>'transportAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, 'transport');
    yii\widgets\Pjax::end();
    ?>

</div>
