<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UserOutlet */

?>
<div class="user-outlet-create">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Outlet</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body" id="user_details">
            <?= $this->render('_form_outlet', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
