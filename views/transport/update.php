<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transport */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Transport',
]) . ' ' . $model->transport_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->transport_id, 'url' => ['view', 'id' => $model->transport_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="transport-update">

    <div class="box box-warning">
        <div class="box-header with-border">
        </div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>


</div>
