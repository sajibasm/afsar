<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Outlet */
?>
<div class="outlet-view">

    <div class="box box-success">
        <div class="box-header with-border">
        </div>
        <div class="box-body" id="reconciliation-create">
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
    </div>


</div>
