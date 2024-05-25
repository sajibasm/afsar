<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Size */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Size',
]) . ' ' . $model->size_name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="size-update">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Size</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="size-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
