<?php

use app\components\BadgeHelper;
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

    <?php Utility::gridViewModal($this, $searchModel); ?>

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
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'outletId',
                'value' => function($model) {
                    return $model->outletDetail->name;
                },
                'pageSummary' => false,
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Date',
                'width' => '120px',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return DateTimeUtility::getDate($model->received_at, SystemSettings::dateTimeFormat());
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Customer',
                'attribute' => 'customer.client_name',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'pageSummary' => false,
                'value'=>function($model){
                    return $model->customer->client_name." (".$model->customer->clientCity->city_name.",".$model->customer->client_address1.")";
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Head',
                'attribute' => 'received_type',
                'format' => 'raw',
                'pageSummary' => false,
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'value' => function($model){
                    return BadgeHelper::render($model->received_type);
                }
            ],

            [
                'header' => 'Type',
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'value' => function ($model) {
                    if($model->paymentType->payment_type_name==\app\models\PaymentType::TYPE_DEPOSIT){
                        $json = (object) Json::decode($model->extra);
                        $bank =  CommonUtility::getBankById($json->bank_id)->bank_name;
                        $branch =  CommonUtility::getBranchById($json->branch_id)->branch_name;
                        return BadgeHelper::render($model->paymentType->payment_type_name).'<br>'. BadgeHelper::render($bank).' '.BadgeHelper::render($branch);
                    }else{
                        return BadgeHelper::render($model->paymentType->payment_type_name);
                    }
                },
            ],


            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'value' => function ($model) {
                    if($model->status==ClientPaymentHistory::STATUS_DECLINED){
                        return BadgeHelper::render('Hold');
                    }
                    return BadgeHelper::render($model->status);
                },
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Remarks',
                'format'=>'raw',
                'attribute' => 'remarks',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'footerOptions' => ['style' => 'text-align: right;'],
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
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: right;'],
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' => true,
                'format'=>['decimal',0],
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Available',
                'attribute' => 'remaining_amount',
                'headerOptions' => ['style' => 'text-align: center; width:100px;'],
                'contentOptions' => ['style' => 'text-align: right;'],
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' => true,
                'format'=>['decimal',0],
            ],


            [
                'class'=>'kartik\grid\ActionColumn',
                'hidden'=>Yii::$app->controller->id=='reports'?true:false,
                'template' => \mdm\admin\components\Helper::filterActionColumn('{approved} {update} {pay} {details} {withdraw} {notification} {print}'),
                'headerOptions' => ['style' => 'text-align: center; width:50px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'hAlign'=>GridView::ALIGN_CENTER,
                'width' => '170px',
                'buttons' => [
                    'update' => function ($url, $model) {
                        if (DateTimeUtility::getDate($model->received_at, 'Y-m-d') == DateTimeUtility::getDate(null, 'Y-m-d') &&
                            $model->status != ClientPaymentHistory::STATUS_Hold &&
                            $model->received_type !=ClientPaymentHistory::RECEIVED_TYPE_SALES &&
                            $model->received_amount == $model->remaining_amount
                        ) {
                            return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->client_payment_history_id)]), [
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Update Payment# ' . $model->received_amount),
                            ]);
                        }
                    },

                    'approved' => function ($url, $model) {
                        if ($model->status == ClientPaymentHistory::STATUS_PENDING) {
                            return \app\components\ButtonHelper::actionButton('approve', '#', [
                                'confirm' => true,
                                'confirmTitle' => 'Are you sure you want to approve this payment?',
                                'confirmText' => 'This action cannot be undone.',
                                'confirmButton' => 'Yes, approve it!',
                                'cancelButton' => 'Cancel',
                                'url' => Url::to(['approve']),
                                'confirmAjax' => 1,
                                'data-id' => Utility::encrypt($model->client_payment_history_id),   // ✅ Send ID separately
                                'pjaxId' => '#customerPaymentHistoryGrid',
                                'title' => Yii::t('app', 'Approve Payment# ' . $model->received_amount),
                            ]);
                        }
                    },

                    'notification' => function ($url, $model) {
                        if ($model->status == ClientPaymentHistory::STATUS_APPROVED) {

                            $isDisabled = true;

                            if($model->received_type==ClientPaymentHistory::RECEIVED_TYPE_DUE_RECEIVED
                                || $model->received_type==ClientPaymentHistory::RECEIVED_TYPE_ADVANCED
                                || $model->received_type==ClientPaymentHistory::RECEIVED_TYPE_SALES_RETURN
                            ){
                                $isDisabled = false;
                            }


                            return \app\components\ButtonHelper::actionButton('notification', '#', [
                                'value' => Url::to(['notification', 'id' => Utility::encrypt($model->client_payment_history_id)]),
                                'data-pjax' => 0,
                                'class' => $isDisabled ? 'disabled' : '',
                                'title' => Yii::t('app', 'Email/SMS Notification'),
                            ]);
                        }
                    },

                    'print' => function ($url, $model) {
                        if ($model->status == ClientPaymentHistory::STATUS_APPROVED) {
                            return \app\components\ButtonHelper::actionButton('print', Url::to(['print', 'id' => Utility::encrypt($model->client_payment_history_id)]), [
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Print Invoice'),
                            ]);
                        }
                    },

                    'pay' => function ($url, $model) {
                        if ($model->status == ClientPaymentHistory::STATUS_APPROVED) {
                            $isDisabled = ($model->remaining_amount <= 0 || $model->status == ClientPaymentHistory::STATUS_PENDING);
                            return \app\components\ButtonHelper::actionButton('pay', Url::to(['pay', 'id' => Utility::encrypt($model->client_payment_history_id)]), [
                                'class' => $isDisabled ? 'disabled' : '',
                                'data-pjax' => 0,
                                'title' => 'Pay invoice-wise or oldest first.',
                            ]);
                        }
                    },

                    'details' => function ($url, $model) {
                        if ($model->status == ClientPaymentHistory::STATUS_APPROVED) {
                            $url = ($model->remaining_amount != $model->received_amount)
                                ? Url::to(['client-payment-details/details', 'id' => Utility::encrypt($model->client_payment_history_id)])
                                : Url::to(['pay', 'id' => Utility::encrypt($model->client_payment_history_id)]);

                            $isDisabled = ($model->remaining_amount == $model->received_amount);
                            return \app\components\ButtonHelper::actionButton('details', $url, [
                                'class' => $isDisabled ? 'disabled' : '',
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => 'Details of this payment',
                            ]);
                        }
                    },

                    'withdraw' => function ($url, $model) {
                        if ($model->status == ClientPaymentHistory::STATUS_APPROVED
                        ) {
                            $url = ($model->remaining_amount > 0)
                                ? Url::to(['withdraw', 'id' => Utility::encrypt($model->client_payment_history_id)])
                                : Url::to(['pay', 'id' => Utility::encrypt($model->client_payment_history_id)]);

                            $isDisabled = false;

                            if($model->received_type == ClientPaymentHistory::RECEIVED_TYPE_RECONCILIATION || ($model->remaining_amount <= 0)){
                                $isDisabled = true;
                            }


                            return \app\components\ButtonHelper::actionButton('withdraw', $url, [
                                'class' => $isDisabled ? 'disabled' : '',
                                'data-pjax' => 0,
                                'title' => 'Cash back remaining amount.',
                            ]);
                        }
                    },

                ],

        ],
        ];

        if(Yii::$app->controller->id=='reports' || Yii::$app->controller->id=='client-payment-history'){
            $colspan = 11;
        }

        yii\widgets\Pjax::begin(['id'=>'customerPaymentHistoryGrid']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();


    ?>

</div>
