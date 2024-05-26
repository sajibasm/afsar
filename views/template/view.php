<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Template */
?>
<div class="template-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'type',
            'name',
            'subject',
            'tags:ntext',
            'body:ntext',
        ],
    ]) ?>

</div>
