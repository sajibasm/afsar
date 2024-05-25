<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Brand */

$this->title = Yii::t('app', 'Brand');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Brands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Brand</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body" id="brand-create">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
