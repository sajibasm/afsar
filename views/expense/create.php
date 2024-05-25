<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Expense */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create">

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Expense Create</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="sales_product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
