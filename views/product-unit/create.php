<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProductUnit */

$this->title = Yii::t('app', 'Product Unit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-unit-create">


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
