<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EmployeeDesignation */

$this->title = Yii::t('app', '{modelClass}: ', [
    'modelClass' => 'Employee Designation',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Role'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="employee-designation-update">

    <div class="box box-info">
        <div class="box-header with-border">
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
