<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EmployeeDesignation */

$this->title = Yii::t('app', 'Employee Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Role'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-designation-create">


    <div class="box box-success">
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
