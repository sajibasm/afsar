<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BankReconciliation */

$this->title = Yii::t('app', 'Reconciliation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Reconciliations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-reconciliation-create">

    <div class="box box-success">
        <div class="box-header with-border">Create</div>
        <div class="box-body" id="bank-reconciliation-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
