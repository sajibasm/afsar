<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Transport */

$this->title = Yii::t('app', 'Transport');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-create">

    <div class="box box-success">
        <div class="box-header with-border">
        </div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
