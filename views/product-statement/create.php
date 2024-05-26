<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProductStatement */

$this->title = Yii::t('app', 'Create Product Statement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Statements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-statement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
