<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentType */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Payment Type',
]) . ' ' . $model->payment_type_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="payment-type-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Expense Type</h3>
            <div class="box-tools pull-right">
            </div>
        </div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
