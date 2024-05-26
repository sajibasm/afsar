<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BankReconciliation */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => ' Reconciliation',
]) . $model->amount;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reconciliations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bank-reconciliation-update">

    <div class="box box-warning">
        <div class="box-header with-border">
        </div>
        <div class="box-body" id="bank-reconciliation-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
