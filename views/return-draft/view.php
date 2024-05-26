<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ReturnDraft */

$this->title = $model->return_draft_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Return Drafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="return-draft-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->return_draft_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->return_draft_id], [
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
            'return_draft_id',
            'sales_id',
            'item_id',
            'brand_id',
            'size_id',
            'refund_amount',
            'total_amount',
            'quantity',
            'user_id',
        ],
    ]) ?>

</div>
