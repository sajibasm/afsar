<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MarketBookHistory */

$this->title = Yii::t('app', 'Create Market Book History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Market Book Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="market-book-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
