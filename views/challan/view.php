<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Challan */

$this->title = $model->challan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Challans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->challan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->challan_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'challan_id',
            'sales_id',
            'client_id',
            'amount',
            'transport_id',
            'transport_invoice_number',
            'condition_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
