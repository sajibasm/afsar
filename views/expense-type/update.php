<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ExpenseType */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Expense Type',
]) . ' ' . $model->expense_type_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expense Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->expense_type_id, 'url' => ['view', 'id' => $model->expense_type_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="expense-type-update">

    <div class="box box-update">
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
