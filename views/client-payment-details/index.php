<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientPaymentDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-payment-details-index">

    <?php
            Utility::gridViewModal($this, $searchModel);
            Utility::getMessage();
            $exportFileName = 'customer-account'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
    ?>

    <?php

    $gridColumns = [

        [
            'class'=>'\kartik\grid\SerialColumn',
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Date',
            'contentOptions' => ['style' => 'width:90px;'],
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'payment_history_id',
            'pageSummary' => false
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'sales_id',
            'pageSummary' => false,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'payment_type',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' => "Total"
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'paid_amount',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format'=>['decimal',0],
        ],

    ];

    if(Yii::$app->controller->id=='report'){
        $colspan = 6;
    }else{
        $colspan = 6;
    }

    $button = null;

    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>



</div>
