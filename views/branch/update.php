<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Branch */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Branch',
]) . ' ' . $model->branch_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Branch'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="branch-update">


    <div class="box box-warning">
        <div class="box-header with-border">
        </div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
