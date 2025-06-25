<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this  yii\web\View */
/* @var $model mdm\admin\models\BizRule */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\BizRule */

$this->title = Yii::t('rbac-admin', 'Rules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">

    <div class="box box-primary">
        <div class="box-header with-border text-center">
            <h3 class="box-title"><?= $this->title ?></h3>
        </div>
        <div class="box-body" id="item-index">

            <div class="row">
                <div class="col-md-11">
                    <p>
                        <?= Html::a(Yii::t('rbac-admin', 'Create Menu'), ['create'], ['class' => 'btn btn-info']) ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="role-index">
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'name',
                                    'label' => Yii::t('rbac-admin', 'Name'),
                                ],
                                ['class' => 'yii\grid\ActionColumn',],
                            ],
                        ]);
                    ?>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>