<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\Menu */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">

        <div class="menu-view">

        <div class="row">
            <div class="col-md-11">
                <p>
                    <?= Html::a(Yii::t('rbac-admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?=
                    Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ])
                    ?>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-11">
                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'menuParent.name:text:Parent',
                        'name',
                        'route',
                        'order',
                    ],
                ])
                ?>
            </div>

        </div>

        </div>
    </div>
</div>
