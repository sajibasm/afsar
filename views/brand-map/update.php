<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BrandMap */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Brand Map',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Brand Maps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="brand-map-update">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Update Brand<?=$model->name?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="brand-new-update">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
