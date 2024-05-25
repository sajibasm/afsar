<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AppSettings */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'App Settings',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'App Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="app-settings-update">

    <div class="box box-warning">
        <div class="box-header with-border">
        </div>
        <div class="box-body" id="reconciliation-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
