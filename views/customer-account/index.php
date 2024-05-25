<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer Payment Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'customer-account'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>


<?php

Utility::gridViewModal($this, $searchModel);

Utility::getMessage();
?>


<div class="customer-account-index">

    <?php
    $gridColumns = [
        ['class' => 'kartik\grid\SerialColumn'],


        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Date',
            'contentOptions' => ['style' => 'width:90px;'],
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return DateTimeUtility::getDate($model->date, SystemSettings::dateTimeFormat());
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Customer',
            'attribute' => 'client_id',
            'pageSummary' => false,
            'contentOptions' => ['style' => 'width:150px;'],
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model) {
                return $model->client->client_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'sales_id',
            'pageSummary' => false,
            'hAlign'=>GridView::ALIGN_CENTER,
            'contentOptions' => ['style' => 'width:80px;']
        ],

//        [
//            'class' => '\kartik\grid\DataColumn',
//            'attribute' => 'memo_id',
//            'pageSummary' => false,
//            'hAlign'=>GridView::ALIGN_CENTER,
//        ],



        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'type',
            'pageSummary' => false,
            'contentOptions' => ['style' => 'width:80px;'],
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model) {
                return $model->type;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Source',
            'contentOptions' => ['style' => 'width:70px;'],
            'attribute' => 'payment_type',
            'pageSummary' => false,
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model) {
                return $model->payment_type;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'account',
            'format'=>'raw',
            'hAlign'=>GridView::ALIGN_CENTER,
            'pageSummary' =>"Total ",
            'value'=>function($model) {
                if(is_int($model->payment_history_id)){
                    return Html::a($model->account, ['client-payment-history/index', 'ClientPaymentHistorySearch[client_payment_history_id]'=>$model->payment_history_id], ['target'=>'_blank', 'data-pjax' => '0']);
                }else{
                    return $model->account;
                }
            }
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'debit',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format'=>['decimal',0],
            'contentOptions' => ['style' => 'width:100px;'],
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'credit',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' =>true,
            'format'=>['decimal',0],
            'contentOptions' => ['style' => 'width:100px;'],
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'balance',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' =>true,
            'format'=>['decimal',0],
            'contentOptions' => ['style' => 'width:100px;'],
        ],


        ];

        $title = 'Customer Payment Statement';
        $header = 'Customer Payment Statement';

        if(Yii::$app->controller->id=='report'){
            $colspan = 10;
        }else{
            $colspan = 11;
        }

        $button = '';

        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);


    ?>


</div>
