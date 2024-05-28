<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\BankReconciliation;
use app\models\PaymentType;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BankReconciliationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\BankReconciliation */

$this->title = Yii::t('app', 'Bank Reconciliations');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'bank_reconcillation_daily_statement'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

?>
<?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="bank-reconciliation-index">

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
                    return $model->id;
                }
            ],


            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Sales Id',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->invoice_id;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'User',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->user->username;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Remarks',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->remarks;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Customer',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->customer->client_name;
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Type',
                'hAlign'=>GridView::ALIGN_CENTER,
                'pageSummary'=>'Total',
                'value'=>function($model){
                    if($model->payment->type== PaymentType::TYPE_DEPOSIT){
                        return $model->bank->bank_name.'('.$model->branch->branch_name.')';
                    }else{
                        return 'Cash';
                    }
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'amount',
                'header'=>'Amount',
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
                'template'=>'{update} {product} {payment} {approved} {print}',
                'buttons' => [

                    'approved' => function ($url, $model) {
                        if($model->status== BankReconciliation::STATUS_PENDING){
                            return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->id)]),[
                                'class'=>'btn btn-default btn-xs approvedButton',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->amount),
                            ]);
                        }
                    },

                    'update' => function ($url, $model) {
                        if(DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y') && $model->status== BankReconciliation::STATUS_PENDING) {
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update','id'=>Utility::encrypt($model->id)]),[
                                'class'=>'btn btn-info btn-xs',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Update '.$this->title.'# '.$model->id),
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

        $button = 'New Payment';
        yii\widgets\Pjax::begin(['id'=>'bankReconciliationPjaxGridView']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>

</div>



