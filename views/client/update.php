<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Client',
]) . ' ' . $model->client_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="client-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="customer_from">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
