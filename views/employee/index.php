<?php

use app\components\ConstrainUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="employee-index">
    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header'=>'#',
            'hAlign'=>GridView::ALIGN_LEFT,
        ],
        [
            'header'=>'Picture',
            'format' => 'html',
            'value' => function($data) { return Html::img($data->getImageUrl(), ['width'=>'60px', 'height'=>'60px']); },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'full_name'
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'designationModel.name',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'contact_number',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'present_address',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'salary',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'joining_date',
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'hiddenFromExport' => true,
            'value' => function ($model) {
                return \app\components\BadgeHelper::render($model->status?'Active':'Inactive');
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'created_at',
        ],
        [
            'class'=>'kartik\grid\ActionColumn',
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'headerOptions' => ['style' => 'text-align: center; width:50px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'template' => \mdm\admin\components\Helper::filterActionColumn('{update}'),
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
        $colspan = 11;
    }else{
        $colspan = 11;
    }

    yii\widgets\Pjax::begin(['id'=>'expensePjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, "employee_role");
    yii\widgets\Pjax::end();
    ?>
</div>
