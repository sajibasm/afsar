<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Branch */

$this->title = Yii::t('app', 'Branch');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branch-create">


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
