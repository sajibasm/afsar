<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerWithdraw */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Customer Withdraw',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer Withdraws'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="customer-withdraw-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
