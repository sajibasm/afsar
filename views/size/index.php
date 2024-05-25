<?php

use app\components\Utility;
use yii\bootstrap\Modal;
    use yii\helpers\Html;
use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
/* @var $searchModel app\models\SizeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sizes');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php

    Utility::gridViewModal($this, $searchModel);
    Utility::getMessage();
?>


<div class="size-index">

    <div class="box box-info">
        <div class="box-header with-border">
            <div class="box-tools pull-left">
                <?= Html::a('Add Size', ['create'], ['class' => 'btn btn-info', 'data-pjax'=>0])?>
            </div>
        </div>
        <div class="box-body">
            <?php yii\widgets\Pjax::begin(['id'=>'pjaxGridView'])?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'header'=>'Image',
                        'format' => 'html',
                        'value' => function($data) { return Html::img($data->getImageUrl(), ['width'=>'100px']); },
                    ],
                    [
                        'attribute'=>'item',
                        'value'=>'item.item_name'

                    ],
                    [
                        'attribute'=>'brand',
                        'value'=>'brand.brand_name'

                    ],

                    'size_name',
                    [
                        'attribute'=>'unit',
                        'value'=>'productUnit.name'
                    ],
                    'unit_quantity',
                    'lowest_price',
                    'size_status',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'Action',
                        'template'=>'{update}  {more}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id'=> Utility::encrypt($model->size_id)],
                                    [
                                        'class'=>'btn btn-info btn-xs',
                                        'data-ajax'=>0,
                                        'data-toggle'=>'tooltip',
                                        'title' => Yii::t('app', "Update ".$model->size_name),
                                    ]
                                );
                            },
                            'more'=>function($url, $model){
                                return Html::button('<span class="glyphicon glyphicon-transfer"></span>', [
                                    'class'=>'btn btn-success btn-xs modalUpdateBtn',
                                    'title' => Yii::t('app', "Details ".$model->size_name),
                                    'id'=>'modalUpdateBtn1',
                                    'data-pjax'=>1,
                                    'value' =>Url::to(['size/view','id'=>Utility::encrypt($model->size_id)]),
                                    'data-toggle'=>'tooltip',
                                ]);

                            }
                        ],
                    ],

                ],
            ]); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>
    </div>

</div>
