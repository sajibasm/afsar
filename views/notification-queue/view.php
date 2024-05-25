<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NotificationQueue */
?>
<div class="notification-queue-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'type',
            'content:ntext',
            'extra_params',
            'status',
            'queue',
            'customerId',
            'message',
            'createdBy',
            'createdAt',
            'updatedAt',
        ],
    ]) ?>

</div>
