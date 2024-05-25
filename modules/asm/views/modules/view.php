<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Modules */
?>
<div class="modules-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',
            'controller',
            'icon',
            'active',
        ],
    ]) ?>

</div>
