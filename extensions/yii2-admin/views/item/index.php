<?php

use yii\helpers\Html;
use yii\grid\GridView;
use mdm\admin\components\RouteRule;
use mdm\admin\components\Configs;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('rbac-admin', $labels['Items']);
$this->params['breadcrumbs'][] = $this->title;

$rules = array_keys(Configs::authManager()->getRules());
$rules = array_combine($rules, $rules);
unset($rules[RouteRule::RULE_NAME]);
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
                        <?= Html::a(Yii::t('rbac-admin', 'Create ' . $labels['Item']), ['create'], ['class' => 'btn btn-info']) ?>
                    </p>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="permission-index">
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
                                [
                                    'attribute' => 'ruleName',
                                    'label' => Yii::t('rbac-admin', 'Rule Name'),
                                    'filter' => $rules
                                ],
                                [
                                    'attribute' => 'description',
                                    'label' => Yii::t('rbac-admin', 'Description'),
                                ],
                                ['class' => 'yii\grid\ActionColumn',],
                            ],
                        ])
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
