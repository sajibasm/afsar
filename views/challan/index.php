<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChallanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Challans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challan-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Challan'), ['create'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'challan_id',
            'sales_id',
            'client_id',
            'amount',
            'transport_id',
            // 'transport_invoice_number',
            // 'condition_id',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'panel' => [
            'type' => 'info',
            'heading' => '<i class="glyphicon glyphicon-list"></i> Sms Gateways listing',
            'before' => '<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>',
        ]
    ]); ?>

</div>
