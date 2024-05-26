<?php

use app\components\SystemSettings;
use app\components\CommonUtility;
use app\components\CustomerUtility;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\ClientPaymentHistory;
use kartik\grid\GridView;
use yii\bootstrap\Alert;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientPaymentHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer Payment History');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'customer'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>
<div class="client-payment-history-index">


    <?php
        Utility::gridViewModal($this, $searchModel);
        Utility::getMessage();
    ?>

    <?php

        $gridColumns = [

//            [
//                'class'=>'\kartik\grid\SerialColumn',
//            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'ID',
                'attribute' => 'client_payment_history_id',
                'pageSummary' => false,
                'contentOptions' => ['style' => 'width:70px;  white-space: normal;']
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'outletId',
                'value' => function($model) {
                    return $model->outletDetail->name;
                },
                'pageSummary' => false,
                'contentOptions' => ['style' => 'width:70px;  white-space: normal;']
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Date',
                'width' => '120px',
                'contentOptions' => ['style' => 'width:100px;  white-space: normal;'],
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return DateTimeUtility::getDate($model->received_at, SystemSettings::dateTimeFormat());
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Customer',
                'attribute' => 'customer.client_name',
                'pageSummary' => false,
                'value'=>function($model){
                    return $model->customer->client_name." (".$model->customer->clientCity->city_name.",".$model->customer->client_address1.")";
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Head',
                'attribute' => 'received_type',
                'pageSummary' => false,
                'contentOptions' => ['style' => 'width:70px;  white-space: normal;']
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Type',
                'width' => '120px',
                'attribute' => 'paymentType.payment_type_name',
                'pageSummary' => false,
                'contentOptions' => ['style' => 'width:50px;  white-space: normal;'],
                'value'=>function($model){
                    if($model->paymentType->payment_type_name==\app\models\PaymentType::TYPE_DEPOSIT){
                        $json = (object) Json::decode($model->extra);
                        $bank =  CommonUtility::getBankById($json->bank_id)->bank_name;
                        $branch =  CommonUtility::getBranchById($json->branch_id)->branch_name;
                        return "{$bank}, {$branch}";
                    }else{
                        return $model->paymentType->payment_type_name;
                    }

                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Status',
                'width' => '30px',
                'attribute' => 'status',
                'pageSummary' => false,
                'contentOptions' => ['style' => 'width:80px;  white-space: normal;'],
                'value'=>function($model){
                    if($model->status==ClientPaymentHistory::STATUS_DECLINED){
                        return "Hold";
                    }
                    return $model->status;
                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Remarks',
                'format'=>'raw',
                'attribute' => 'remarks',
                'pageSummary' =>"Total ",
                'value'=>function($model){
                    if($model->status==ClientPaymentHistory::STATUS_DECLINED){
                            return "<b>Transaction Block</b>, Approve Customer Withdraw ID: ".CustomerUtility::hasWithdrawByPaymentId($model->client_payment_history_id);
                    }else{
                        if($model->received_type==ClientPaymentHistory::RECEIVED_TYPE_SALES_RETURN){
                            if(!empty($model->remarks)){
                                return $model->remarks."( Invoice# {$model->sales_id})";
                            }else{
                                return "Invoice# {$model->sales_id})";
                            }
                        }else{
                            return $model->remarks;
                        }
                    }
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Received',
                'attribute' => 'received_amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' => true,
                'format'=>['decimal',0],
                'contentOptions' => ['style' => 'width:100px;  white-space: normal;'],
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Available',
                'attribute' => 'remaining_amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' => true,
                'format'=>['decimal',0],
                'contentOptions' => ['style' => 'width:100px;  white-space: normal;'],
            ],


            [
                'class'=>'kartik\grid\ActionColumn',
                'hidden'=>Yii::$app->controller->id=='reports'?true:false,
                'template' => '{approved} {update} {pay} {details} {withdraw} {notification} {print}',
                'hAlign'=>GridView::ALIGN_CENTER,
                'width' => '170px',
                'buttons' => [

                    'update' => function ($url, $model) {
                        if(DateTimeUtility::getDate($model->received_at, 'Y-m-d')==DateTimeUtility::getDate(null, 'Y-m-d')  && $model->status!=ClientPaymentHistory::STATUS_Hold){
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->client_payment_history_id)], ['class'=>'btn btn-info btn-xs', 'data-ajax'=>0]);
                        }
                    },

                    'approved' => function ($url, $model) {
                        if($model->status==ClientPaymentHistory::STATUS_PENDING){
                            return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->client_payment_history_id)]),[
                                'class'=>'btn btn-default btn-xs approvedButton',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->received_amount),
                            ]);
                        }
                    },

                    'notification' => function ($url, $model) {
                        if($model->status==ClientPaymentHistory::STATUS_APPROVED){
                            return Html::button('<span class="fa fa-paper-plane"></span>', [
                                'class'=>'btn btn-primary btn-xs modalUpdateBtn',
                                'title' => Yii::t('app', 'Email/SMS Notification'),
                                'data-pjax'=>0,
                                'value' =>Url::to(['notification','id'=>Utility::encrypt($model->client_payment_history_id)])
                            ]);
                        }
                    },

                    'print' => function ($url, $model) {

                        if($model->status==ClientPaymentHistory::STATUS_APPROVED){
                            return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['print','id'=>Utility::encrypt($model->client_payment_history_id)]),[
                                'class'=>'btn btn-success btn-xs',
                                'title' => Yii::t('app', 'Print Invoice'),
                                'data-pjax'=>0,
                                'target'=>'_blank'
                            ]);
                        }
                    },


                    'pay' => function ($url, $model) {
                        if($model->status==ClientPaymentHistory::STATUS_APPROVED){
                            if($model->remaining_amount>0 && $model->status!=ClientPaymentHistory::STATUS_PENDING){
                                return Html::a('<span class="glyphicon glyphicon-import"></span>', ['pay', 'id'=> Utility::encrypt($model->client_payment_history_id)],
                                    [
                                        'class'=>'btn btn-success btn-xs',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Pay invoice-wise or oldest is fast.'

                                    ]);
                            }else{
                                return Html::a('<span class="glyphicon glyphicon-import"></span>', ['pay', 'id'=> Utility::encrypt($model->client_payment_history_id)],
                                    [
                                        'class'=>'btn btn-success btn-xs disabled',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Pay invoice-wise or oldest is fast.'

                                    ]);
                            }
                        }
                    },
                    'details' => function ($url, $model) {
                        if($model->status==ClientPaymentHistory::STATUS_APPROVED){
                            if($model->remaining_amount!=$model->received_amount){
                                return Html::a('<span class="glyphicon glyphicon-transfer"></span>', ['client-payment-details/details', 'id'=> Utility::encrypt($model->client_payment_history_id)],
                                    [
                                        'class'=>'btn btn-primary btn-xs',
                                        'data-ajax'=>"0",
                                        "target"=>"_blank",
                                        //'data-toggle'=>'tooltip',
                                        'title'=>'Details of this payment'

                                    ]);
                            }else{
                                return Html::a('<span class="glyphicon glyphicon-transfer"></span>', ['pay', 'id'=> Utility::encrypt($model->client_payment_history_id)],
                                    [
                                        'class'=>'btn btn-primary btn-xs disabled',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Details of this payment'

                                    ]);
                            }
                        }
                    },
                    'withdraw' => function ($url, $model) {
                        if($model->status==ClientPaymentHistory::STATUS_APPROVED){
                            if($model->remaining_amount>0){
                                return Html::a('<span class="glyphicon glyphicon-export"></span>', ['withdraw', 'id'=> Utility::encrypt($model->client_payment_history_id)],
                                    [
                                        'class'=>'btn btn-danger btn-xs',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Cash back remaining amount.'

                                    ]);
                            }else{
                                return Html::a('<span class="glyphicon glyphicon-export"></span>', ['pay', 'id'=> Utility::encrypt($model->client_payment_history_id)],
                                    [
                                        'class'=>'btn btn-danger btn-xs disabled',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title'=>'Cash back remaining amount.'

                                    ]);
                            }
                        }
                    }
                ],

        ],
        ];

        if(Yii::$app->controller->id=='reports' || Yii::$app->controller->id=='client-payment-history'){
            $colspan = 10;
        }

        $button = [];

        yii\widgets\Pjax::begin(['id'=>'customerPaymentHistoryGrid']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();


    ?>

</div>
