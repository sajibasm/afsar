<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Buyer */

$this->title = Yii::t('app', 'Supplier');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Supplier'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-create">

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Supplier</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>



</div>
