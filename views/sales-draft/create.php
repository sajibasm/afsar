<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SalesDraft */

$this->title = Yii::t('app', 'Create Sales Draft');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Drafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-draft-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
