<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ChallanCondition */

$this->title = Yii::t('app', 'Challan Condition');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Challan Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challan-condition-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
