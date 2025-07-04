<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\BankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Banks');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>


<div class="bank-index">

    <?php
    $gridColumns = [
        [
            'class' => '\kartik\grid\SerialColumn',
            'hAlign'=>GridView::ALIGN_CENTER
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'bank_name',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],

        [
            'class'=>'kartik\grid\ActionColumn',
            //'hidden'=>true,
            'vAlign'=>GridView::ALIGN_RIGHT,
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'template'=>'{update} ',
            'headerOptions' => ['style' => 'text-align: center; width:50px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'urlCreator' => function ($action, $model, $key, $index) {
                return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
            },
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
        $colspan = 3;
    }

    yii\widgets\Pjax::begin(['id'=>'bankAjax']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, 'bank');
    yii\widgets\Pjax::end();
    ?>

</div>
