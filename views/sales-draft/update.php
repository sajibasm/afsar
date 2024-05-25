<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDraft */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Sales Draft',
]) . $model->sales_details_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Drafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sales_details_id, 'url' => ['view', 'id' => $model->sales_details_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sales-draft-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
