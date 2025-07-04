<?php

use app\components\ConstrainUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmployeeDesignationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employee Roles');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="employee-designation-index">
    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header'=>'#',
            'hAlign'=>GridView::ALIGN_LEFT,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name'
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'hiddenFromExport' => true,
            'value' => function ($model) {
                return \app\components\BadgeHelper::render(ConstrainUtility::ROLE_STATUS_LIST[$model->status]);
            },
        ],
        [
            'class'=>'kartik\grid\ActionColumn',
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'headerOptions' => ['style' => 'text-align: center; width:50px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Record# ' . $model->id),
                    ]);
                },
            ],
        ],
    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 10;
    }else{
        $colspan = 10;
    }

    yii\widgets\Pjax::begin(['id'=>'expensePjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, "employee_role");
    yii\widgets\Pjax::end();
    ?>


</div>
