<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CashBook */

$this->title = Yii::t('app', 'Cash Book Summery');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Book Summery'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<style>
    .jumbotron {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>


<div class="cash-book-create">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Summery</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <div class="jumbotron">
            <?= $this->render('_summery', [
                'model' => $model,
            ]) ?>
            </div>

        </div>
    </div>


</div>
