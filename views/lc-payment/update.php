<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LcPayment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => '',
]) . $model->lc->lc_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lc Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="lc-payment-update">


    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">LC Payment Update</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
