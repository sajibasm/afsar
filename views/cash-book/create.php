<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CashBook */

$this->title = Yii::t('app', 'Create Cash Book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-book-create">
    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
