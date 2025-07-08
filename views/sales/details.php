<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\checkbox\CheckboxX;
use app\models\SalesDetails;
use app\models\CustomerAccount;

/* ----------------------------------------------------------
   Data providers (unchanged)
---------------------------------------------------------- */
$customerDataProvider = new ActiveDataProvider([
    'query'      => CustomerAccount::find()->where(['sales_id' => $salesId])->orderBy('id ASC'),
    'pagination' => false,
]);
?>

<div class="container-fluid bg-light p-3">

    <!-- ───────────────────────── 1. TABLE ROW ────────────────────────── -->
    <div class="row">

        <div class="col-md-5 mb-4">
            <div class="box box-danger">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Goods Sold Details</h3>
                </div>
                <div class="box-body p-0">
                    <?= GridView::widget([
                        'dataProvider'    => $salesDataProvider,
                        'summary'         => '',
                        'tableOptions'    => ['class' => 'table table-bordered table-hover table-striped mb-0'],
                        'columns'         => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'header' => 'Item',
                                'value'  => fn($m) => $m->item->item_name ?? '',
                            ],
                            [
                                'header' => 'Brand',
                                'value'  => fn($m) => $m->brand->brand_name ?? '',
                            ],
                            [
                                'header' => 'Size',
                                'value'  => fn($m) => $m->size->size_name ?? '',
                            ],
                            [
                                'header'         => 'Unit Price',
                                'value'          => fn($m) => number_format($m->sales_amount, 2),
                                'contentOptions' => ['class' => 'text-right'],
                            ],
                            [
                                'header'         => 'Qty',
                                'value'          => fn($m) => number_format($m->quantity, 2),
                                'contentOptions' => ['class' => 'text-center'],
                            ],
                            [
                                'header'         => 'Total',
                                'value'          => fn($m) => number_format($m->total_amount, 2),
                                'contentOptions' => ['class' => 'text-right'],
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="box box-success">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Payment Details</h3>
                </div>
                <div class="box-body p-0">
                    <?php
                    // 1. Calculate Total Amount in Controller or View
                    $totalAmount = 0;
                    foreach ($clientTransactionSummary->getModels() as $model) {
                        $totalAmount += $model->transaction_amount;
                    }
                    ?>


                    <?= GridView::widget([
                        'dataProvider' => $clientTransactionSummary,
                        'summary'      => '',
                        'showFooter'   => true,   // ✅ Enable footer
                        'tableOptions' => ['class' => 'table table-bordered table-hover table-striped mb-0'],
                        'columns'      => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'created_at',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'contentOptions' => ['style' => 'text-align: center;'],
                            ],
                            [
                                'attribute' => 'transaction_type',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'contentOptions' => ['style' => 'text-align: center;'],
                            ],
                            [
                                'attribute' => 'transaction_mode',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'contentOptions' => ['style' => 'text-align: center;'],
                            ],
                            [
                                'attribute' => 'reference_table',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'contentOptions' => ['style' => 'text-align: center;'],
                            ],
                            [
                                'attribute' => 'remarks',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'contentOptions' => ['style' => 'text-align: center;'],
                            ],
                            [
                                'attribute' => 'user.username',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'contentOptions' => ['style' => 'text-align: center;'],
                            ],
                            [
                                'attribute' => 'transaction_amount',
                                'headerOptions' => ['style' => 'text-align: center;'],
                                'footer' => Yii::$app->formatter->asDecimal($totalAmount, 2),  // ✅ Sum displayed here
                                'footerOptions' => ['style' => 'text-align: right;'],
                                'contentOptions' => ['style' => 'text-align: right;'], // Optional: align cell values too
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>


    </div><!-- /.row -->

</div><!-- /.container-fluid -->
