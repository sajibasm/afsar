<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\CashHandReceived;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CashHandReceivedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cash Hand Received');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'cash_hand_received_statement_'.DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="cash-hand-received-index">

    <?php

        $gridColumns = [
            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#',
                //'hiddenFromExport'=>true,
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Date',
                'attribute' => 'created_at',
                'pageSummary' => false,
                'hAlign'=>GridView::ALIGN_CENTER,
                'contentOptions' => ['style' => 'width:100px;'],
                'value'=>function($model){
                    return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'outletId',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->outlet->name;
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'User',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return ($model->user)? $model->user->username : '';
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => 'Remarks',
                'hAlign'=>GridView::ALIGN_CENTER,
                'pageSummary' => "Total",
                'value'=>function($model){
                    return $model->remarks;
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'received_amount',
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
                'headerOptions' => ['style' => 'text-align: center; width:50px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'template'=>'{approved} {update} {product} {payment} {print}',
                'buttons' => [
                    'approved' => function ($url, $model) {
                        if ($model->status == CashHandReceived::STATUS_PENDING) {
                            return \app\components\ButtonHelper::actionButton('approve', '#', [
                                'confirm' => true,
                                'confirmTitle' => 'Are you sure you want to approve this cash hand received?',
                                'confirmText' => 'This action cannot be undone.',
                                'confirmButton' => 'Yes, approve it!',
                                'cancelButton' => 'Cancel',
                                'class' => 'btn-confirm',  // Required for SweetAlert trigger
                                'url' => Url::to(['cash-hand-received/approved', 'id' => Utility::encrypt($model->id)]), // ✅ Approve action endpoint
                                'confirmAjax' => 1,         // Enables AJAX
                                'pjaxId' => '#cashHandReceivedGrid',  // Optional PJAX container ID to refresh
                                'title' => Yii::t('app', 'Approve ' . '# ' . $model->received_amount),
                            ]);
                        }
                    },
                    'update' => function ($url, $model) {
                        if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')) {
                            return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                                'class' => '',
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Update Withdraw# ' . $model->received_amount),
                            ]);
                        } else {
                            return 'N/A';
                        }
                    },
                ],
            ],
        ];

        if(Yii::$app->controller->id=='reports'){
            $colspan = 7;
        }else{
            $colspan = 7;
        }

        yii\widgets\Pjax::begin(['id'=>'cashHandReceivedGrid']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>

</div>
