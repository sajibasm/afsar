<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ReconciliationType */

$this->title = Yii::t('app', 'Create Reconciliation Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reconciliation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Reconciliation-type-create">


    <div class="box box-success">
        <div class="box-header with-border">
        </div>
        <div class="box-body" id="reconciliation-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>



</div>
