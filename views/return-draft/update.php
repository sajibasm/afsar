<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReturnDraft */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Return Draft',
]) . ' ' . $model->return_draft_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Return Drafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->return_draft_id, 'url' => ['view', 'id' => $model->return_draft_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="return-draft-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
