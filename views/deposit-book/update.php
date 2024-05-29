<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DepositBook */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Deposit Book',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Deposit Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="deposit-book-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
