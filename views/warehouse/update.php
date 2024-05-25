<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Warehouse',
]) . ' ' . $model->warehouse_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="warehouse-update">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Warehouse</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
