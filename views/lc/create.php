<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Lc */

$this->title = Yii::t('app', 'Add LC');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lc-create">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">LC</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="product_details">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
