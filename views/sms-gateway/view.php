<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SmsGateway */
?>
<div class="sms-gateway-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'url:url',
            'apiKey',
            'senderId',
            'balance',
            'updateAt',
            'status',
        ],
    ]) ?>

</div>
