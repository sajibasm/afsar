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

<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

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
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'status',
            'value'=>function($model){
                return ConstrainUtility::EMPLOYEE_STATUS_LIST[$model->status];
            }
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'created_at',
        ],
        [
            'class'=>'kartik\grid\ActionColumn',
            'hiddenFromExport'=>true,
            'hAlign'=>GridView::ALIGN_CENTER,
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->id)]),[
                        'class'=>'btn btn-info btn-xs',
                        'data-pjax'=>0,
                    ]);
                }
            ],
        ],
    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 10;
    }else{
        $colspan = 10;
    }

    $button = 'New Employee';

    yii\widgets\Pjax::begin(['id'=>'expensePjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, "employee_role");
    yii\widgets\Pjax::end();
    ?>
</div>
