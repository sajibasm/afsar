<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SalaryHistory */

$this->title = Yii::t('app', 'Advance Salary Withdraw');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Salary Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-history-create">

    <?= $this->render('_daily', [
        'model' => $model,
    ]) ?>

</div>
