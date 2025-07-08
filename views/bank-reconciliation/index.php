<?php

use app\components\ButtonHelper;
use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\BankReconciliation;
use app\models\PaymentType;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
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
<?php Utility::gridViewModal($this, $searchModel); ?>


<div class="bank-reconciliation-index">

    <?php

        $gridColumns = [

            [
                'class' => 'kartik\grid\SerialColumn',
                'header'=>'#',
                'hAlign'=>GridView::ALIGN_LEFT,
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
                'header' => 'Store',
                'hAlign'=>GridView::ALIGN_CENTER,
                'value'=>function($model){
                    return $model->outlet->name;
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
                'attribute' => 'id',
                'hAlign'=>GridView::ALIGN_CENTER,
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
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'status',
                'pageSummary' =>true,
                'vAlign'=>GridView::ALIGN_RIGHT,
                'hAlign'=>GridView::ALIGN_CENTER,
            ],


            [
                'class'=>'kartik\grid\ActionColumn',
                //'hidden'=>true,
                'vAlign'=>GridView::ALIGN_RIGHT,
                'hiddenFromExport'=>true,
                'hAlign'=>GridView::ALIGN_CENTER,
                'template' => Helper::filterActionColumn('{approve} {update}'),
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
                },
                'buttons' => [
                    'update' => function ($url, $model) {
                        if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y') && $model->status!==BankReconciliation::STATUS_DELETE) {
                            return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Update'),
                            ]);
                        }
                    },

                    'approve' => function ($url, $model) {
                        if ($model->status == BankReconciliation::STATUS_PENDING) {
                            return ButtonHelper::actionButton('approve', '#', [
                                'confirm' => true,
                                'confirmTitle' => 'Are you sure?',
                                'confirmText' => 'Do you want to approve Reconciliation ID #' . $model->id . '?',
                                'confirmButton' => 'Yes, approve it!',
                                'cancelButton' => 'Cancel',
                                'class' => 'btn-confirm',  // ✅ Ensure btn-confirm is here for JS trigger
                                'url' => Url::to(['approve']),
                                'data-id' => Utility::encrypt($model->id),   // ✅ Send ID separately
                                'confirmAjax' => 1,                        // ✅ Enable AJAX call in confirm-buttons.js
                                'pjaxId' => '#bankReconciliation',                 // ✅ Optional: PJAX container if you want to reload something
                                'title' => Yii::t('app', 'Approve action !'),
                            ]);
                        }
                    },
                ],
            ],

        ];

        if(Yii::$app->controller->id=='report'){
            $colspan = 10;
        }else{
            $colspan = 11;
        }

        yii\widgets\Pjax::begin(['id'=>'bankReconciliation']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>

</div>



