<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Buyer */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Buyer',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Buyers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="buyer-update">

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Supplier</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
