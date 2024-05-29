<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */

$this->title = Yii::t('app', 'Warehouse');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-create">

    <div class="box box-success">
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
