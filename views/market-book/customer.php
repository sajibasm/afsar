<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MarketBook */

$this->title = Yii::t('app', 'Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Market Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="market-book-create">


    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Customer</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="size-create">
            <?= $this->render('_customer', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
