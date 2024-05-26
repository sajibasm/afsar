<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ReturnDraft */

$this->title = Yii::t('app', 'Create Return Draft');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Return Drafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="return-draft-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
