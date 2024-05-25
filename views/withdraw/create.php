<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Withdraw */

$this->title = Yii::t('app', 'Create Withdraw');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Withdraws'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-create">


    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Withdraw</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>



</div>
