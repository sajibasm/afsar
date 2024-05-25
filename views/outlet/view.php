<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Outlet */
?>
<div class="outlet-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'outletId',
            'outletCode',
            'name',
            'address1',
            'address2',
            'logo',
            'logoWaterMark',
            'contactNumber',
            'email:email',
            'status',
        ],
    ]) ?>

</div>
