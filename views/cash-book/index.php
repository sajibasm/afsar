<?php

use app\components\SystemSettings;
use app\components\DateTimeUtility;
use app\components\Utility;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CashBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'Cash Statement');
$this->params['breadcrumbs'][] = $this->title;
$exportFileName = 'cash_book_statement' . DateTimeUtility::getDate(null, 'd-M-Y_h:s:A');

?>


<?php

Utility::gridViewModal($this, $searchModel);

Utility::getMessage();
?>


<div class="client-payment-history-index">

    <?php

    if (Yii::$app->controller->id == 'report') {
        $colspan = 7;
    } else {
        $colspan = 7;
    }

    $button = '';

    $gridColumns = [

        [
            'class' => 'kartik\grid\SerialColumn',
            'header' => '#',
            'hAlign' => GridView::ALIGN_LEFT,
            //'pageSummary'=>true,
            //'pageSummaryFunc'=>GridView::F_COUNT,
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
            'header' => 'ID',
            'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model, $key, $value) {
                return $model->id;
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Outlet',
            'attribute' => 'outletId',
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {
                return $model->outletDetail->name;
            }
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'source',
            //'pageSummary' => false,
            'pageSummary' => false,
            'hAlign' => GridView::ALIGN_CENTER,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'header' => 'Remarks(Ref)',
            'attribute' => 'remarks',
            'pageSummary' => "Total",
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model, $key, $value) {
                if (empty($model->remarks)) {
                    return "Ref ID:" . $model->reference_id;
                }
                return $model->remarks . "(Ref ID:" . $model->reference_id . ")";
            }
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'cash_in',
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'pageSummaryOptions' => ['append' => " " . Yii::$app->params['currency']],
            'format' => ['decimal', 0],
            'pageSummaryFunc' => GridView::F_SUM,
        ],

        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'cash_out',
            'hAlign' => GridView::ALIGN_RIGHT,
            'pageSummary' => true,
            'pageSummaryOptions' => ['append' => " " . Yii::$app->params['currency']],
            'format' => ['decimal', 0],
            'pageSummaryFunc' => GridView::F_SUM,
            'vAlign' => 'middle',
        ],


    ];

    echo Utility::gridViewWidget($dataProvider, $gridColumns, $button, $this->title, $colspan, $exportFileName);

    ?>

</div>
