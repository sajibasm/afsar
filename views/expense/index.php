<?php

use app\components\ButtonHelper;
use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\Expense;
use app\models\ExpenseType;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ExpenseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Expense Statement');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'expense_daily_statement'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

?>

<?php Utility::gridViewModal($this, $searchModel); ?>


<div class="expense-index">
    <?php
        $gridColumns = [

            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#',
                'hAlign'=>GridView::ALIGN_LEFT,
                //'pageSummary'=>true,
                //'pageSummaryFunc'=>GridView::F_COUNT,
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
                'header' => 'Outlet',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->outlet->name;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'ID',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->expense_id;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'User',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return (!$model->user) ? '' : $model->user->username;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Remarks',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->expense_remarks;
                }
            ],


            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Expense Type',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->expenseType->expense_type_name;
                }
            ],


            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Type',
                'hAlign'=>GridView::ALIGN_CENTER,
                'pageSummary' =>"Total",
                'value'=>function($model){
                    return $model->type;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'expense_amount',
                'header'=>'Amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',0],
            ],


            [
                'class'=>'kartik\grid\ActionColumn',
                'hidden'=>Yii::$app->controller->id=='reports'?true:false,
                'vAlign'=>GridView::ALIGN_RIGHT,
                'hiddenFromExport'=>true,
                'hAlign'=>GridView::ALIGN_CENTER,
                'template' => Helper::filterActionColumn('{approved} {update} {print}'),
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
                },
                'buttons' => [

                    'approved' => function ($url, $model) {
                        if ($model->status == Expense::STATUS_PENDING) {
                            return \app\components\ButtonHelper::actionButton('approve', Url::to(['approved', 'id' => Utility::encrypt($model->expense_id)]), [
                                'confirm' => true,
                                'confirmAjax' => true,                 // ✅ Enable SweetAlert + AJAX
                                'pjaxId' => '#expensePjaxGridView',      // ✅ Target PJAX reload
                                'confirmTitle' => 'Confirm?',
                                'confirmText' => 'Approve this expense?',
                                'confirmButton' => 'Yes, approve',
                                'cancelButton' => 'No',
                                'title' => Yii::t('app', 'Approve Expense# ' . $model->expense_amount),
                            ]);
                        }
                        return '';  // Always return something to avoid errors in GridView
                    },

                    'update' => function ($url, $model) {
                        $isToday = DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y');
                        return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->expense_id)]), [
                            'disabled' => !$isToday,
                            'title' => Yii::t('app', 'Update Expense# ' . $model->expense_amount),
                        ]);
                    },

                    'print' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('print', Url::to(['invoice', 'id' => Utility::encrypt($model->expense_id)]), [
                            'target' => '_blank',
                            'title' => Yii::t('app', 'Print Invoice'),
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
        echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>


</div>
