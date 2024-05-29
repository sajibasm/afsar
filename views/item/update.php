<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Item */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Item',
]) . ' ' . $model->item_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->item_id, 'url' => ['view', 'id' => $model->item_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>


<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Item</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body" id="item-create">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
