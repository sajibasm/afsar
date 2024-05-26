<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Challan */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Challan',
]) . ' ' . $model->challan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Challans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->challan_id, 'url' => ['view', 'id' => $model->challan_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="challan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
