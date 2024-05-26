<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Size */

$this->title = $model->size_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sizes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel panel-success">
    <div class="panel-heading">

    </div>

    <div class="panel-body">

        <div class="size-view">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Image',
                        'format'=>'Image',
                        'value' => $model->getImageUrl(false)
                    ],
                    [
                        'label' => 'Item',
                        'value' => $model->item->item_name
                    ],
                    [
                        'label' => 'Brand',
                        'value' => $model->brand->brand_name
                    ],
                    [
                        'label' => 'Size',
                        'value' => $model->size_name
                    ],
                    [
                        'label' => 'unit',
                        'value' => $model->unit
                    ],
                    [
                        'label' => 'unit_quantity',
                        'value' => $model->unit_quantity
                    ],
                    [
                        'label' => 'lowest_price',
                        'value' => $model->lowest_price."%"
                    ],
                    [
                        'label' => 'size_status',
                        'value' => $model->size_status
                    ],
                    [
                        'label' => 'Product Details',
                        'format'=>'html',
                        'value' => $model->size_description
                    ],

                    [
                        'label' => 'Status',
                        'value' => $model->size_status==0?'Active':'Inactive'
                    ],
                ]
            ]) ?>

        </div>
    </div>

    <div class="panel-footer">

        <div class="modal-footer">
            <?= Html::Button(Yii::t('app', 'Close'), ['class'=>'btn btn-default', 'aria-hidden'=>true, 'data-dismiss'=>'modal'])?>
        </div>

    </div>