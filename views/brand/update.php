<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Brand */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Brand',
]) . ' ' . $model->brand_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Brands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->brand_id, 'url' => ['view', 'id' => $model->brand_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>


<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Brand</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body" id="brand-update">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>