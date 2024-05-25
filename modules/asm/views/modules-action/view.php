<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ModulesAction */
?>
<div class="modules-action-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'module',
            'code',
            'name',
            'active',
        ],
    ]) ?>

</div>
