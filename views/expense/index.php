<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\Expense;
use app\models\ExpenseType;
use kartik\grid\GridView;
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

<?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>

<div class="expense-index">


    <?php

        $button = 'Create';

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
                'template'=>'{approved}  {update} {print}',
                'buttons' => [
                    'approved' => function ($url, $model) {
                        if($model->status== Expense::STATUS_PENDING){
                            return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->expense_id)]),[
                                'class'=>'btn btn-default btn-xs approvedButton',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->expense_amount),
                            ]);
                        }
                    },
                    'update' => function ($url, $model) {
                        $disabled = '';
                        if(DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y')) {
                            $class = 'btn btn-info btn-xs';
                        }else{
                            $class = 'btn btn-default btn-xs disabled';
                        }

                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->expense_id)]),[
                            'class'=>$class,
                            'data-pjax'=>0,
                            'title' => Yii::t('app', 'Update LC Payment# '.$model->expense_amount),
                        ]);
                    },
                    'print' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['invoice','id'=>Utility::encrypt($model->expense_id)]),[
                            'class'=>'btn btn-default btn-xs',
                            'title' => Yii::t('app', 'Print Invoice'),
                            'data-pjax'=>0,
                            'target'=>'_blank'
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

        $button = 'New Payment';

        yii\widgets\Pjax::begin(['id'=>'expensePjaxGridView']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>


</div>
