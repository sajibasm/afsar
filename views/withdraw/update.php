<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Withdraw */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Withdraw',
]) . $model->withdraw_amount;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Withdraws'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="withdraw-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Withdraw - Create</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>


</div>
