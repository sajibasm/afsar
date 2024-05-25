<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="customer-account-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'',
        'showHeader'=>true,
        'showFooter'=>false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'sales_id',
            'memo_id',
            //'client.client_name',
            'type',
            'payment_type',
            'account',
            'debit',
            'credit',
            'balance',
            'date',
        ],
    ]); ?>

</div>
