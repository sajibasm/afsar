<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ModulePermission */
?>
<div class="module-permission-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'userId',
            'module',
            'module_action_id',
            'createdAt',
            'createdBy',
        ],
    ]) ?>

</div>
