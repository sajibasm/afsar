<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SalesReturnDetails */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Sales Return Details',
]) . ' ' . $model->sales_details_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Return Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sales_details_id, 'url' => ['view', 'id' => $model->sales_details_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sales-return-details-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
