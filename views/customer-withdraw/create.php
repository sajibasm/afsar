<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CustomerWithdraw */

$this->title = Yii::t('app', 'Create Customer Withdraw');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer Withdraws'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-withdraw-create">

    <div class="box box-success">
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
