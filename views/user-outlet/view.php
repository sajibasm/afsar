<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UserOutlet */
?>
<div class="user-outlet-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'userOutletId',
            'userId',
            'outletId',
            'createdBy',
            'updatedBy',
            'cretaedAt',
            'updatedAt',
        ],
    ]) ?>

</div>
