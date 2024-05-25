<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BrandMap */

$this->title = Yii::t('app', 'Create Brand Map');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Brand Maps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-map-create">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Create Brand</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="create-brand-new">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
