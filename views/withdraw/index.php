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

<?php

Utility::gridViewModal($this, $searchModel);

Utility::getMessage();
?>


<div class="withdraw-index">

    <?php

    $button = 'New Withdraw';

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
            'header' => 'Outlet',
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
            'template' => '{approved} {update}',
            'buttons' => [

                'approved' => function ($url, $model) {
                    if ($model->status == Withdraw::STATUS_PENDING) {
                        return Html::a('<span class="fa fa-check"></span>', Url::to(['view', 'id' => Utility::encrypt($model->id)]), [
                            'class' => 'btn btn-default btn-xs approvedButton',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->withdraw_amount),
                        ]);
                    }
                },

                'update' => function ($url, $model) {
                    if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['withdraw/update', 'id' => Utility::encrypt($model->id)]), [
                            'class' => 'btn btn-info btn-xs',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Update Withdraw# ' . $model->withdraw_amount),
                        ]);
                    } else {
                        return 'N/A';
                    }
                }
            ],

        ],

    ];

    if (Yii::$app->controller->id == 'report') {
        $colspan = 9;
    } else {
        $colspan = 9;
    }


    yii\widgets\Pjax::begin(['id' => 'withdrawAjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);
    yii\widgets\Pjax::end();


    ?>


</div>
