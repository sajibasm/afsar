<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ClientPaymentHistory */

$this->title = Yii::t('app', 'Received Payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-payment-history-create">


    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $this->title ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>


</div>
