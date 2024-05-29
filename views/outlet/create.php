<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Outlet */

?>
<div class="outlet-create">

    <div class="box box-success">
        <div class="box-header with-border">
        </div>
        <div class="box-body" id="reconciliation-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
