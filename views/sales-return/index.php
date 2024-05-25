<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\SalesReturn;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesReturnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Return-Service Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'sales_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>


<?php
    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="sales-return-index">

    <?php

        $gridColumns = [
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'sales_return_id',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'outletId',
                'value' => function($model) {
                    return $model->outletDetail->name;
                },
                'hAlign'=>GridView::ALIGN_CENTER,
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
                'header'=>'User',
                'attribute' => 'user_id',
                //'pageSummary' =>"Total ",
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return ($model->user) ? $model->user->username : '';
                }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Invoice',
                'attribute' => 'sales_id',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header'=>'Type',
                'attribute' => 'type',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Customer',
                'attribute' => 'client_name',
                'hAlign'=>GridView::ALIGN_CENTER,
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Remarks',
                'attribute' => 'remarks',
                'hAlign'=>GridView::ALIGN_CENTER,
                'pageSummary' =>"Total ",
                'filterType'=>GridView::FILTER_DATE_RANGE
            ],



            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Refund',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',0],
                'value'=>function($data){
                    if($data->refund_amount>0){
                        return $data->refund_amount;
                    }else{
                        return 0;
                    }

                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Adjust/Recon',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'pageSummary' =>true,
                'format'=>['decimal',0],
                'value'=>function($data){
                    return $data->cut_off_amount;
                },
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Total',
                'hAlign'=>GridView::ALIGN_RIGHT,
                'format'=>['decimal',0],
                'pageSummary' =>true,
                'value'=>function($data){
                    return abs($data->total_amount);
                },
            ],

            [
                'class'=>'kartik\grid\ActionColumn',
                'hidden'=>Yii::$app->controller->id=='reports'?true:false,
                'vAlign'=>GridView::ALIGN_RIGHT,
                'hiddenFromExport'=>true,
                'hAlign'=>GridView::ALIGN_CENTER,
                'template'=>'{approved} {update} {product} {payment} ',
                'buttons' => [

                    'approved' => function ($url, $model) {
                        if($model->status== SalesReturn::STATUS_PENDING){
                            return Html::a('<span class="fa fa-check"></span>', Url::to(['view','id'=>Utility::encrypt($model->sales_return_id)]),[
                                'class'=>'btn btn-default btn-xs approvedButton',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Approve '.$this->title.'# '.$model->total_amount),
                            ]);
                        }
                    },

                    'update' => function ($url, $model) {
                        if($model->status== SalesReturn::STATUS_PENDING){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['sales-return/update','id'=>$model->sales_id]),[
                                'class'=>'btn btn-primary btn-xs',
                                'data-pjax'=>0,
                                'title' => Yii::t('app', 'Update Invoice# '.$model->sales_id. ' Customer: '.$model->client_name),
                            ]);
                        }
                    },

                    'product' => function ($url, $model) {

                        return Html::button('<span class="glyphicon glyphicon-list"></span>', [
                                'class'=>'btn btn-info btn-xs modalUpdateBtn',
                                'title' => Yii::t('app', 'Product Details'),
                                'data-pjax'=>0,
                                'value' =>Url::to(['sales-details/details','id'=>Utility::encrypt($model->sales_return_id)])
                            ]).'</li>';
                    },

                    'payment' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-list-alt"></span>', [
                            'class'=>'btn btn-success btn-xs modalUpdateBtn',
                            'title' => Yii::t('app', 'Payment Details'),
                            'data-pjax'=>0,
                            'value' =>Url::to(['customer-account/details','id'=>Utility::encrypt($model->sales_return_id)])
                        ]);
                    },

                    'print' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['sales/print','id'=>Utility::encrypt($model->sales_return_id)]),[
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

        $button = [
            Html::a(Yii::t('app', 'Return'),['/sales-return/verify'], ['class' => 'btn btn-success', 'data-pjax'=>0]),
            Html::a(Yii::t('app', 'Service'),['/sales-return/verify-repair'], ['class' => 'btn btn-primary', 'data-pjax'=>0])
        ];


    yii\widgets\Pjax::begin(['id'=>'salesReturn']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
    yii\widgets\Pjax::end();





    ?>

</div>
