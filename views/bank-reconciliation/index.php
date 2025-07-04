<?php

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
                'template' => Helper::filterActionColumn('{approved} {update} {product} {payment} {print}'),
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to([$action, 'id' => \app\components\Utility::encrypt($key)]);
                },
                'buttons' => [
                    'update' => function ($url, $model) {
                        if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y') && $model->status == BankReconciliation::STATUS_PENDING) {
                            return \app\components\ButtonHelper::actionButton('update', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                                'data-pjax' => 0,
                                'title' => Yii::t('app', 'Update'),
                            ]);
                        }
                    },

                    'approved' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('approve', '#', [
                            'confirm' => true,                                     // ✅ Enable confirmation
                            'confirmTitle' => 'Are you sure you want to approve?',
                            'confirmText' => 'This action cannot be undone.',
                            'confirmButton' => 'Yes, approve',
                            'cancelButton' => 'Cancel',
                            'url' => Url::to(['approved', 'id' => Utility::encrypt($model->id)]),  // ✅ Set real approval URL
                            'confirmAjax' => 1,                                     // ✅ Use AJAX instead of redirect
                            'pjaxId' => '#bankReconciliationPjaxGridView',                       // ✅ Optional: reload PJAX container
                            'title' => Yii::t('app', 'Approve Item# ' . $model->id),
                        ]);
                    },

                    'product' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('details', '#', [
                            'value' => $url,
                            'title' => Yii::t('app', 'Item Details.'),
                            'data-pjax' => 0,
                        ]);
                    },

                    'payment' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('payment', '#', [
                            'value' => $url,
                            'title' => Yii::t('app', 'Payment Details'),
                            'data-pjax' => 0,
                        ]);
                    },

                    'print' => function ($url, $model) {
                        return \app\components\ButtonHelper::actionButton('print', $url, [
                            'target' => '_blank',
                            'title' => Yii::t('app', 'Print Invoice'),
                            'data-pjax' => 0,
                        ]);
                    },

                ],


            ],

        ];

        if(Yii::$app->controller->id=='report'){
            $colspan = 10;
        }else{
            $colspan = 11;
        }

        yii\widgets\Pjax::begin(['id'=>'bankReconciliationPjaxGridView']);
        echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
        yii\widgets\Pjax::end();
    ?>

</div>



