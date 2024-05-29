<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SalesDetails */

$this->title = Yii::t('app', 'Create Sales Details');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-details-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
