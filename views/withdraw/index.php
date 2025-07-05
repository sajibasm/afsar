<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\FlashMessage;
use app\components\Utility;
use app\models\Withdraw;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WithdrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Withdraw');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'withdraws_statement_' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');
?>

<?php Utility::gridViewModal($this, $searchModel); ?>

<div class="withdraw-index">

    <?php

    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
            //'hiddenFromExport'=>true,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Date',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return DateTimeUtility::getDate($model->created_at, SystemSettings::dateTimeFormat());
            }
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'outletId',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->outlet->name;
            }
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'User',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return ($model->user) ? $model->user->username : '';
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Remarks',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->remarks;
            }
        ],


        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Type',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->type;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Bank',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                if (isset($model->bank->bank_name)) {
                    return $model->bank->bank_name;
                } else {
                    return "";
                }
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Branch',
            'hAlign' => GridView::ALIGN_CENTER,
            'pageSummary' => "Total",
            'value' => function ($model) {
                if (isset($model->branch->branch_name)) {
                    return $model->branch->branch_name;
                } else {
                    return "";
                }
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'withdraw_amount',
            'header' => 'Amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'vAlign' => GridView::ALIGN_RIGHT,
            'hAlign' => GridView::ALIGN_CENTER,
            'hiddenFromExport' => true,
            'headerOptions' => ['style' => 'text-align: center; width:100px;'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'template' => \mdm\admin\components\Helper::filterActionColumn('{approved} {update}'),
            'buttons' => [
                'approved' => function ($url, $model) {
                    if ($model->status == Withdraw::STATUS_PENDING) {
                        return \app\components\ButtonHelper::actionButton('approve', '#', [
                            'confirm' => true,
                            'confirmTitle' => 'Are you sure you want to approve this withdrawal?',
                            'confirmText' => 'This action cannot be undone.',
                            'confirmButton' => 'Yes, approve it!',
                            'cancelButton' => 'Cancel',
                            'class' => 'btn-confirm',  // ✅ Must-have class for SweetAlert to trigger
                            'url' => Url::to(['withdraw/approved', 'id' => Utility::encrypt($model->id)]),  // ✅ Actual approve action URL
                            'confirmAjax' => 1,         // ✅ Enables AJAX instead of page redirect
                            'pjaxId' => '#withdrawGrid', // ✅ Optional: reload PJAX container if you have one
                            'title' => Yii::t('app', 'Approve Withdrawal# ' . $model->withdraw_amount),
                        ]);
                    }
                },
                'update' => function ($url, $model) {
                    if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')) {
                        return \app\components\ButtonHelper::actionButton('update', Url::to(['withdraw/update', 'id' => Utility::encrypt($model->id)]), [
                            'class' => '',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Update Withdraw# ' . $model->withdraw_amount),
                        ]);
                    } else {
                        return 'N/A';
                    }
                },
            ],

        ],

    ];

    if (Yii::$app->controller->id == 'report') {
        $colspan = 10;
    } else {
        $colspan = 10;
    }


    yii\widgets\Pjax::begin(['id' => 'withdrawGrid']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, false, $this->title, $colspan, $exportFileName);
    yii\widgets\Pjax::end();


    ?>


</div>
