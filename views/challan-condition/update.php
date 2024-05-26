<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ChallanCondition */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Challan Condition',
]) . ' ' . $model->challan_condition_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Challan Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="challan-condition-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
