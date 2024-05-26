<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\SalaryHistory;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalaryHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Advance Salary');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'salary-history'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>
<?php

    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="salary-history-index">

<?php

    $gridColumns = [
    [
        'class' => 'kartik\grid\SerialColumn',
        'header'=>'#',
    ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Date',
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'ID',
            'attribute' => 'id',
            'hAlign'=>GridView::ALIGN_CENTER,
        ],


        [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'User',
        'pageSummary' => false,
        'hAlign'=>GridView::ALIGN_CENTER,
        'value'=>function($model){
            return $model->user->username;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Employee',
        'pageSummary' => false,
        'hAlign'=>GridView::ALIGN_CENTER,
        'value'=>function($model){
            return $model->employee->full_name;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Remarks',
        'pageSummary' => false,
        'hAlign'=>GridView::ALIGN_CENTER,
        'value'=>function($model){
            return $model->remarks;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Month',
        'pageSummary' => false,
        'hAlign'=>GridView::ALIGN_CENTER,
        'value'=>function($model){
            return $model->month;
        }
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Year',
        'pageSummary' => "Total ",
        'hAlign'=>GridView::ALIGN_CENTER,
        'value'=>function($model){
            return $model->year;
        }
    ],


    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'withdraw_amount',
        'header' => 'Advanced/Paid',
        'hAlign'=>GridView::ALIGN_RIGHT,
        'pageSummary' =>true,
        'format'=>['decimal',0],
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Remaining',
        'attribute' => 'remaining_salary',
        'hAlign'=>GridView::ALIGN_RIGHT,
        'pageSummary' =>true,
        'format'=>['decimal',0],
    ],


    [
        'class'=>'kartik\grid\ActionColumn',
        //'hidden'=>true,
        'vAlign'=>GridView::ALIGN_RIGHT,
        'hiddenFromExport'=>true,
        'hAlign'=>GridView::ALIGN_CENTER,
        'template'=>'{approved} {update}',
        'buttons' => [
            'approved' => function ($url, $model) {
                if($model->status== SalaryHistory::STATUS_PENDING){
                    return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->id)]),[
                        'class'=>'btn btn-default btn-xs approvedButton',
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->withdraw_amount),
                    ]);
                }
            },

            'update' => function ($url, $model) {
                if(DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y') && Yii::$app->controller->id!=='reports'){
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update','id'=> Utility::encrypt($model->id)]),[
                        'class'=>'btn btn-primary btn-xs',
                        'data-pjax'=>0,
                        'title' => Yii::t('app', 'Update Salary History# '.$model->id),
                    ]);
                }
            }
        ],

    ],

];

    if(Yii::$app->controller->id=='report'){
        $colspan = 10;
    }else{
        $colspan = 10;
    }

    $button = 'New Salary';

    $button = [
    ];
    
    yii\widgets\Pjax::begin(['id'=>'employeeWithdrawPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
    yii\widgets\Pjax::end();
?>


</div>
