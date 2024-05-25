<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CashHandReceived */

$this->title = Yii::t('app', 'Cash Hand Received');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Hand Received'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-hand-received-create">


    <div class="box box-success">
        <div class="box-header with-border">Cash Hand Received</div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
