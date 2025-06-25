<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Menu */

$this->title = Yii::t('rbac-admin', 'Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <div class="box box-primary">
        <div class="box-header with-border text-center">
            <h3 class="box-title"><?= $this->title ?></h3>
        </div>

        <div class="box-body" id="menu-index">
            <div class="row">
                <div class="col-md-11">
                    <p>
                        <?= Html::a(Yii::t('rbac-admin', 'Create Menu'), ['create'], ['class' => 'btn btn-info']) ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php Pjax::begin(); ?>
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'name',
                            [
                                'attribute' => 'menuParent.name',
                                'filter' => Html::activeTextInput($searchModel, 'parent_name', [
                                    'class' => 'form-control', 'id' => null
                                ]),
                                'label' => Yii::t('rbac-admin', 'Parent'),
                            ],
                            'route',
                            'order',
                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]);
                    ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
