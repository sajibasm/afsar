<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LcPaymentType */

$this->title = Yii::t('app', 'Update {modelClass}', [
    'modelClass' => 'Lc Payment Type',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lc Payment Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="lc-payment-type-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">LC Payment Type</h3>
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
