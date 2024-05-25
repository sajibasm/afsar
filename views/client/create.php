<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = Yii::t('app', 'Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">


    <div class="box box-info">
        <div class="box-header with-border">
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="customer_from">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>




</div>
