<?php

use app\components\CommonUtility;
use app\components\Utility;
use app\models\SalesReturnDetails;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="sales-details-index">
    <?php
    $totalReturnedAmount = 0;
    $totalAdjustedAmount = 0;

    foreach ($dataProvider->models as $model) {
        $returnedQty = SalesReturnDetails::getTotalReturnedQty($model->sales_id, $model->size_id);
        $remainingQty = $model->quantity - $returnedQty;
        $adjusted = max($remainingQty, 0) * $model->sales_amount;
        $returned = $returnedQty * $model->sales_amount;

        $totalReturnedAmount += $returned;
        $totalAdjustedAmount += $adjusted;
    }

    $totalAmount = CommonUtility::pageTotal($dataProvider->models, 'total_amount');
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'font-weight:bold;'],
        'rowOptions' => function($model) {
            $returnedQty = \app\models\SalesReturnDetails::getTotalReturnedQty($model->sales_id, $model->size_id);
            if ($returnedQty >= $model->quantity) {
                return ['class' => 'danger']; // full return
            } elseif ($returnedQty > 0) {
                return ['class' => 'warning']; // partial return
            }
            return []; // no return
        },

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'item_id',
                'value'=>function($model){
                    return $model->item->item_name;
                },
            ],
            [
                'attribute'=>'brand_id',
                'value'=>function($model){
                    return $model->brand->brand_name;
                },
            ],
            [
                'attribute'=>'size_id',
                'value'=>function($model){
                    return $model->size->size_name;
                },
            ],

            [
                'header' => 'Qty',
                'footer' => 'Adjusted',
                'value' => function($model) {
                    $returnedQty = SalesReturnDetails::getTotalReturnedQty($model->sales_id, $model->size_id);
                    $remaining = $model->quantity - $returnedQty;
                    return $remaining > 0 ? $remaining : 0;
                },
            ],
            [
                'header' => 'Return Qty',
                'footer' => $totalReturnedAmount,
                'value' => function($model) {
                    return SalesReturnDetails::getTotalReturnedQty($model->sales_id, $model->size_id);
                },
            ],
            [
                'attribute' => 'sales_amount',
                'footer' => 'Total',
            ],
            [
                'attribute' => 'total_amount',
                'footer' => $totalAmount,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Return',
                'template'=>'{return}',
                'buttons' => [
                    'return' => function ($url, $model) {
                        $returnedQty = SalesReturnDetails::getTotalReturnedQty($model->sales_id, $model->size_id);
                        $remaining = $model->quantity - $returnedQty;
                        if($remaining > 0){
                            return Html::button('<span class="fas fa-arrow-down"></span>', [
                                'class'=>'btn btn-warning btn-xs modalUpdateBtn',
                                'title' => Yii::t('app', $model->item->item_name.' Update'),
                                'id'=>'modalUpdateBtn1',
                                'data-pjax'=>1,
                                'value' => Url::to(['/sales-return/items', 'id' => Utility::encrypt($model->sales_details_id)])
                            ]);
                        }
                        return '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
