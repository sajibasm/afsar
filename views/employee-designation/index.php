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
<?php
Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

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
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'status',
            'value'=>function($model){
                return ConstrainUtility::ROLE_STATUS_LIST[$model->status];
            }
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

    $button = 'New Role';

    yii\widgets\Pjax::begin(['id'=>'expensePjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, "employee_role");
    yii\widgets\Pjax::end();
    ?>


</div>
