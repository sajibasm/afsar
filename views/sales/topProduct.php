<?php

use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\Sales;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales Statement');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'sales_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>


<?php

    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="sales-index">

    <?php

        if(Yii::$app->controller->id=='report'){
            $colSpan = 15;
        }else{
            $colSpan = 18;
        }

        $button = 'New Sell';

        $gridColumns = [
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'sales_id',
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'contentOptions' => ['style' => 'width:80px;']
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'created_at',
                    'pageSummary' => false,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'contentOptions' => ['style' => 'width:100px;']
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'user_id',
                    'pageSummary' => false,
                    'hiddenFromExport'=>true,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'value'=>function($model){
                        return $model->user->username;
                    },
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'client_name',
                    'pageSummary' => false,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'contentOptions' => ['style' => 'width:150px;'],
                    'noWrap' => false
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'contact_number',
                    'pageSummary' => false,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'contentOptions' => ['style' => 'width:110px;']
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'transport_name',
                    'pageSummary' => false,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'contentOptions' => ['style' => 'width:110px;']
                ],
                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'tracking_number',
                    'pageSummary' => false,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'contentOptions' => ['style' => 'width:110px;']
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'payment_type',
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'pageSummary' =>"Total ",
                    'value'=>function($model){
                        return $model->paymentTypeModel->type;
                    },
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'paid_amount',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>[
                        'prepend'=>''
                    ]

                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'due_amount',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>[
                        'prepend'=>''
                    ]
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'discount_amount',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>[
                        'prepend'=>''
                    ]
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'reconciliation_amount',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>[
                        'prepend'=>''
                    ]
                ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'sales_return_amount',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>[
                        'prepend'=>''
                    ]
                ],


                [
                    'class' => '\kartik\grid\DataColumn',
                    'header' => 'Refund',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>['prepend'=>''],
                    'value'=>function($data){
                            if(($data->received_amount+$data->reconciliation_amount+$data->sales_return_amount)>($data->total_amount-$data->discount_amount)){
                                return ($data->received_amount+$data->reconciliation_amount+$data->sales_return_amount) - ($data->total_amount-$data->discount_amount);
                            }else{
                                return 0;
                            }
                    },
                ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'received_amount',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',0],
                'contentOptions' => ['style' => 'width:100px;'],
                'pageSummaryOptions'=>[
                    'prepend'=>''
                ]
            ],

                [
                    'class' => '\kartik\grid\DataColumn',
                    'header' => 'Total Dues',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'format'=>['decimal',0],
                    'contentOptions' => ['style' => 'width:100px;'],
                    'pageSummaryOptions'=>[
                        'prepend'=>''
                    ],
                    'value'=>function($data){
                        if(($data->received_amount+$data->reconciliation_amount+$data->sales_return_amount)>($data->total_amount-$data->discount_amount)){
                            return ($data->received_amount+$data->reconciliation_amount+$data->sales_return_amount) - ($data->total_amount-$data->discount_amount) + ($data->total_amount - $data->discount_amount) - ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount);
                        }else {
                            return ($data->total_amount - $data->discount_amount) - ($data->received_amount + $data->reconciliation_amount + $data->sales_return_amount);
                        }
                    },
                ],


                [
                    'class' => '\kartik\grid\DataColumn',
                    'attribute' => 'total_amount',
                    'hAlign'=>GridView::ALIGN_RIGHT,
                    'pageSummary' =>true,
                    'contentOptions' => ['style' => 'width:120px;'],
                    'format'=>['decimal',0],
                    'pageSummaryOptions'=>[
                            'prepend'=>''
                    ]
                ],


            [
                    'class'=>'kartik\grid\ActionColumn',
                    //'hidden'=>true,
                    'vAlign'=>GridView::ALIGN_RIGHT,
                    'hiddenFromExport'=>true,
                    'hAlign'=>GridView::ALIGN_CENTER,
                    'template'=>' {approved} {update} {product} {payment} {transport} {notification} {print}',
                    'buttons' => [

                        'approved' => function ($url, $model) {
                        if($model->status== Sales::STATUS_PENDING){
                            return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->sales_id)]),[
                                'class'=>'btn btn-default btn-xs approvedButton',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->sales_id),
                            ]);
                            }
                        },

                        'update' => function ($url, $model) {
                                if((DateTimeUtility::getDate($model->created_at, 'd-m-Y')==DateTimeUtility::getDate(null, 'd-m-Y') && Yii::$app->controller->id!=='reports') || $model->type== Sales::TYPE_SALES || $model->type== Sales::TYPE_SALES_UPDATE){
                                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['sales/update','id'=> Utility::encrypt($model->sales_id)]),[
                                        'class'=>'btn btn-default btn-xs',
                                        'data-pjax'=>0,
                                        'title' => Yii::t('app', 'Update Invoice# '.$model->sales_id. ' Customer: '.$model->client_name),
                                    ]);
                                }
                        },

                        'product' => function ($url, $model) {
                            return Html::button('<span class="fa fa-product-hunt"></span>', [
                                    'class'=>'btn btn-primary btn-xs modalUpdateBtn',
                                    'title' => Yii::t('app', 'Item Details.'),
                                    'data-pjax'=>0,
                                    'value' =>Url::to(['sales-details/details','id'=>Utility::encrypt($model->sales_id)])
                                ]).'</li>';
                        },

                        'payment' => function ($url, $model) {
                            return Html::button('<span class="fa fa-credit-card-alt"></span>', [
                                'class'=>'btn btn-success btn-xs modalUpdateBtn',
                                'title' => Yii::t('app', 'Payment Details'),
                                'data-pjax'=>0,
                                'value' =>Url::to(['customer-account/details','id'=>Utility::encrypt($model->sales_id)])
                            ]);
                        },

                        'transport' => function ($url, $model) {
                            if($model->status== Sales::STATUS_APPROVED){
                                return Html::button('<span class="fa fa-truck"></span>', [
                                    'class'=>'btn btn-default btn-xs modalUpdateBtn',
                                    'title' => Yii::t('app', 'Transport Details'),
                                    'data-pjax'=>0,
                                    'value' =>Url::to(['sales/transport','id'=>Utility::encrypt($model->sales_id)])
                                ]);
                            }
                        },

                        'notification' => function ($url, $model) {
                            if($model->status== Sales::STATUS_APPROVED) {
                                return Html::button('<span class="fa fa-paper-plane"></span>', [
                                    'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                                    'title' => Yii::t('app', 'Email/SMS Notification'),
                                    'data-pjax' => 0,
                                    'value' => Url::to(['sales/notification', 'id' => Utility::encrypt($model->sales_id)])
                                ]);
                            }

                        },

                        'print' => function ($url, $model) {
                            if($model->status== Sales::STATUS_APPROVED) {
                                return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['sales/print','id'=>Utility::encrypt($model->sales_id)]),[
                                    'class'=>'btn btn-success btn-xs',
                                    'title' => Yii::t('app', 'Print Invoice'),
                                    'data-pjax'=>0,
                                    'target'=>'_blank'
                                ]);
                            }
                        },
                    ],

                ],
        ];

    yii\widgets\Pjax::begin(['id'=>'salesPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colSpan, $exportFileName);
    yii\widgets\Pjax::end();



    ?>


</div>
