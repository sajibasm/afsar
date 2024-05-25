<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Expense */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Expense',
]) . ' ' . $model->expenseType->expense_type_name." : ".$model->expense_amount;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="expense-update">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Expense Update</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
