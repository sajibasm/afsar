<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CashHandReceived */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Cash Hand Received',
]) . $model->received_amount;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Hand Receiveds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="cash-hand-received-update">


    <div class="box box-warning">
        <div class="box-header with-border">Cash Hand Received</div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
