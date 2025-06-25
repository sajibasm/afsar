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
$salesDataProvider = new ActiveDataProvider([
    'query'      => SalesDetails::find()->where(['sales_id' => $salesId]),
    'pagination' => false,
]);

$customerDataProvider = new ActiveDataProvider([
    'query'      => CustomerAccount::find()->where(['sales_id' => $salesId])->orderBy('id ASC'),
    'pagination' => false,
]);
?>

<div class="container-fluid bg-light p-3">

    <!-- ───────────────────────── 1. TABLE ROW ────────────────────────── -->
    <div class="row">

        <div class="col-lg-6 mb-4">
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

        <div class="col-lg-6 mb-4">
            <div class="box box-success">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">Payment Details</h3>
                </div>
                <div class="box-body p-0">
                    <?= GridView::widget([
                        'dataProvider' => $customerDataProvider,
                        'summary'      => '',
                        'tableOptions' => ['class' => 'table table-bordered table-hover table-striped mb-0'],
                        'columns'      => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'memo_id',
                            'type',
                            'payment_type',
                            'account',
                            [
                                'attribute'       => 'debit',
                                'value'           => fn($m) => number_format($m->debit, 2),
                                'contentOptions'  => ['class' => 'text-right'],
                            ],
                            [
                                'attribute'       => 'credit',
                                'value'           => fn($m) => number_format($m->credit, 2),
                                'contentOptions'  => ['class' => 'text-right'],
                            ],
                            [
                                'attribute'       => 'balance',
                                'value'           => fn($m) => number_format($m->balance, 2),
                                'contentOptions'  => ['class' => 'text-right'],
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div><!-- /.row -->


    <div class="row">
        <div class="col-lg-4 col-lg-offset-8 mb-4">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Send Notification</h3>
                </div>

                <div class="box-body">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'id' => 'notification-form',
                        'action' => ['/sales/notification', 'id' => $salesId],
                        'options' => ['class' => 'form-horizontal']
                    ]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="display: flex; align-items: center;">
                                <div style="margin-right: 8px;">
                                    <?= \kartik\checkbox\CheckboxX::widget([
                                        'name' => 'Sales[email]',
                                        'options' => ['id' => 'notif-email'],
                                        'pluginOptions' => ['threeState' => false],
                                    ]) ?>
                                </div>
                                <label for="notif-email" class="mb-0 font-weight-bold">
                                    Email <small>(attach invoice PDF)</small>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" style="display: flex; align-items: center;">
                                <div style="margin-right: 8px;">
                                    <?= \kartik\checkbox\CheckboxX::widget([
                                        'name' => 'Sales[sms]',
                                        'options' => ['id' => 'notif-sms'],
                                        'pluginOptions' => ['threeState' => false],
                                    ]) ?>
                                </div>
                                <label for="notif-sms" class="mb-0 font-weight-bold">
                                    SMS <small>(send text alert)</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-right">
                        <?= \yii\helpers\Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                        <?= \yii\helpers\Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
                    </div>

                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>


</div><!-- /.container-fluid -->
