<?php

    use yii\helpers\Html;
    /* @var $this yii\web\View */
    /* @var $model app\models\Item */

    $this->title = Yii::t('app', 'Item');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>


<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Item</h3>
        <div class="box-tools pull-right"></div>
    </div>
    <div class="box-body" id="item-create">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
