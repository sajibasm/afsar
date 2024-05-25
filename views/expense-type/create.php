<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ExpenseType */

$this->title = Yii::t('app', 'Expense Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expense Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-type-create">

    <div class="box box-success">
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
