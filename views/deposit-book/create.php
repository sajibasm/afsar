<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DepositBook */

$this->title = Yii::t('app', 'Create Deposit Book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Deposit Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deposit-book-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
