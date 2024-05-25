<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use app\models\CustomerWithdraw;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerWithdrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Refund');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php

Utility::gridViewModal($this, $searchModel);
Utility::getMessage();
?>

<div class="customer-withdraw-index">

    <?php Pjax::begin(); ?>
    <?php

    $gridColumns = [
        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'ID',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->id;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'outletId',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->outletDetail->name;
            }
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
            'header' => 'Payment ID',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->payment_history_id;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Created',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return ($model->user) ? $model->user->username : '';
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Approved',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return ($model->updatedUser) ? $model->updatedUser->username : '';
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
            'header' => 'Status',
            'hAlign' => GridView::ALIGN_CENTER,
            'pageSummary' => "Total",
            'value' => function ($model) {
                return $model->status;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Type',
            'hAlign' => GridView::ALIGN_CENTER,
            'pageSummary' => "Total",
            'value' => function ($model) {
                return $model->type;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Amount',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'format' => ['decimal', 0],
            'value' => function ($model) {
                return $model->amount;
            }
        ],


        [
            'class' => 'kartik\grid\ActionColumn',
            'hidden' => Yii::$app->controller->id == 'reports' ? true : false,
            'width' => '120px',
            'vAlign' => GridView::ALIGN_RIGHT,
            'hiddenFromExport' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'template' => '{approved}  {update} {print}',
            'buttons' => [
                'approved' => function ($url, $model) {
                    if ($model->status == CustomerWithdraw::STATUS_PENDING) {
                        return Html::a('<span class="fa fa-check"></span>', Url::to(['view', 'id' => Utility::encrypt($model->id)]), [
                            'class' => 'btn btn-default btn-xs approvedButton',
                            'data-pjax' => 0,
                            'title' => Yii::t('app', 'Approve ' . $this->title . '# ' . $model->amount),
                        ]);
                    }
                },

                'print' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['print', 'id' => Utility::encrypt($model->id)]), [
                        'class' => 'btn btn-success btn-xs',
                        'title' => Yii::t('app', 'Print Payment Refund Receipt'),
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
                },

                'update' => function ($url, $model) {
                    $disabled = '';
                    if (DateTimeUtility::getDate($model->created_at, 'd-m-Y') == DateTimeUtility::getDate(null, 'd-m-Y')
                        && Yii::$app->controller->id != 'reports'
                        && $model->status == CustomerWithdraw::STATUS_PENDING
                    ) {
                        $class = 'btn btn-info btn-xs';
                    } else {
                        $class = 'btn btn-default btn-xs disabled';
                    }

                    return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['update', 'id' => Utility::encrypt($model->id)]), [
                        'class' => $class,
                        'data-pjax' => 0,
                        'title' => Yii::t('app', 'Update  Payment# ' . $model->amount),
                    ]);
                }
            ],

        ],

    ];

    if (Yii::$app->controller->id == 'reports') {
        $colspan = 8;
    } else {
        $colspan = 8;
    }

    $button = null;

    yii\widgets\Pjax::begin(['id' => 'customerWithdrawPjaxGridView']);
    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, "customer-credit-statement");
    yii\widgets\Pjax::end();
    ?>

    <?php Pjax::end(); ?>

</div>
