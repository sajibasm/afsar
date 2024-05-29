<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductUnit */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product Unit',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-unit-update">

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
