<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReconciliationType */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Receoncliation Type',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Receoncliation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="receoncliation-type-update">

    <div class="box box-info">
        <div class="box-header with-border">
        </div>
        <div class="box-body" id="reconciliation-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
