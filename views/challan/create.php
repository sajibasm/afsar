<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Challan */

$this->title = Yii::t('app', 'Create Challan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Challans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
