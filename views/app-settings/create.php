<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AppSettings */

$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'App Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-settings-create">

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
