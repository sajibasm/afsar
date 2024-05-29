<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="user-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'last_name',
            'username',
            'user_image',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
