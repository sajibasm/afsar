<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lc */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Lc',
]) . ' ' . $model->lc_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lc_id, 'url' => ['view', 'id' => $model->lc_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="lc-update">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">LC</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
