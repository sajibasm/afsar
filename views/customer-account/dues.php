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

$this->title = Yii::t('app', 'Dues Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'customer-dues'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>


<?php
    Utility::gridViewModal($this, $searchModel, '_dues_search');
    Utility::getMessage();
?>


<div class="customer-account-index">

    <?php
    $gridColumns = [
        ['class' => 'kartik\grid\SerialColumn'],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Total Days',
            'contentOptions' => ['style' => 'width:90px;'],
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                $date = isset($model->clientPaymentHistory->received_at)?$model->clientPaymentHistory->received_at:date('Y-m-d');
                return DateTimeUtility::countDown($date);
            }
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Date',
            'contentOptions' => ['style' => 'width:90px;'],
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model){
                $date = isset($model->clientPaymentHistory->received_at)?$model->clientPaymentHistory->received_at:date('Y-m-d');
                return DateTimeUtility::getDate($date, SystemSettings::dateTimeFormat());
            }
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Customer',
            'attribute' => 'client_id',
            'pageSummary' => "Total",
            'hAlign'=>GridView::ALIGN_CENTER,
            'value'=>function($model) {
                return $model->client->client_name;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Total Dues',
            'attribute' => 'balance',
            'hAlign'=>GridView::ALIGN_RIGHT,
            'pageSummary' =>true,
            'format'=>['decimal',0],
        ],

    ];

        $title = 'Dues Statement';
        $header = 'Dues Statement';

        if(Yii::$app->controller->id=='report'){
            $colspan = 4;
        }else{
            $colspan = 5;
        }

        $button = '';

        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);


    ?>


</div>
