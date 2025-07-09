<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var int $clientId */

?>

<div class="sales-due-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'hover' => true,
        'striped' => true,
        'showPageSummary' => true, // <-- enable summary row

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'sales_id',
                'label' => 'Invoice No',
                'hAlign' => 'center',
                'contentOptions' => ['style' => 'width:100px;'],
            ],

            [
                'attribute' => 'client_name',
                'label' => 'Customer Name',
                'value' => function ($model) {
                    return $model->client_name ?? $model->client->client_name ?? '-';
                },
            ],

            [
                'attribute' => 'total_amount',
                'hAlign' => 'right',
                'format' => ['decimal', 0],
            ],

            [
                'attribute' => 'discount_amount',
                'label' => 'Discount',
                'hAlign' => 'right',
                'format' => ['decimal', 0],
            ],

            [
                'attribute' => 'paid_amount',
                'hAlign' => 'right',
                'format' => ['decimal', 0],
            ],

            [
                'attribute' => 'reconciliation_amount',
                'label' => 'Recon',
                'hAlign' => 'right',
                'format' => ['decimal', 0],
            ],

            [
                'attribute' => 'sales_return_amount',
                'label' => 'Return',
                'hAlign' => 'right',
                'format' => ['decimal', 0],
            ],

            [
                'label' => 'Remaining Due',
                'format' => ['decimal', 0],
                'hAlign' => 'right',
                'pageSummary' => true, // <-- this adds total

                'value' => function ($model) {
                    $netPayable = $model->total_amount - $model->discount_amount;
                    $adjusted = $model->paid_amount + $model->reconciliation_amount + $model->sales_return_amount;
                    return max(0, $netPayable - $adjusted);
                },
                'contentOptions' => ['style' => 'background-color: #fef7d3'],
                'pageSummaryOptions' => ['style' => 'font-weight:bold; background-color: #fff4c2'],

            ],

            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d M Y h:i A'],
                'label' => 'Date',
            ],
        ],
    ]); ?>

</div>
