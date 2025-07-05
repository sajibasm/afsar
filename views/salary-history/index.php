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

$this->title = Yii::t('app', 'Payroll');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'salary-history'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php Utility::gridViewModal($this, $searchModel); ?>


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
        'template' => \mdm\admin\components\Helper::filterActionColumn('{approved} {update}'),
        'headerOptions' => ['style' => 'text-align: center; width:50px;'],
        'contentOptions' => ['style' => 'text-align: center;'],
        'buttons' => [
            'approved' => function ($url, $model) {
                if ($model->status == SalaryHistory::STATUS_PENDING) {
                    return \app\components\ButtonHelper::actionButton('approve', '#', [
                        'confirm' => true,
                        'confirmTitle' => 'Are you sure you want to approve this salary?',
                        'confirmText' => 'This action cannot be undone.',
                        'confirmButton' => 'Yes, approve it!',
                        'cancelButton' => 'Cancel',
                        'url' => Url::to(['approved', 'id' => Utility::encrypt($model->id)]),  // You can set to ['view'] if you prefer simple view link
                        'confirmAjax' => 1,                // Optional: set to 0 if you don't need AJAX
                        'pjaxId' => '#employeeWithdrawPjaxGridView',         // Optional PJAX reload container
                        'title' => Yii::t('app', 'Approve'),
                    ]);
                }
            },

            'update' => function ($url, $model) {
                if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y') && Yii::$app->controller->id !== 'reports') {
                    return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update Salary History# ' . $model->id),
                    ]);
                }
            },
        ],

    ],

];

    if(Yii::$app->controller->id=='report'){
        $colspan = 13;
    }else{
        $colspan = 13;
    }

    
    yii\widgets\Pjax::begin(['id'=>'employeeWithdrawPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
    yii\widgets\Pjax::end();
?>


</div>
