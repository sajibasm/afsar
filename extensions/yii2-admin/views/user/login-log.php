<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Activity Log';
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="box box-primary">
        <div class="box-header with-border"></div>
        <div class="box-body" id="user-logs-index">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'ip_address',
                    'format' => 'html',
                    'value' => fn($model) => "<span class='badge badge-primary'>{$model->ip_address}</span>",
                ],
                [
                    'label' => 'Location',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::encode("{$model->city}, {$model->region}, {$model->country}");
                    }
                ],
                [
                    'attribute' => 'country_flag',
                    'format' => 'raw',
                    'value' => fn($model) => $model->country_flag ? Html::img($model->country_flag, ['width' => 30, 'style' => 'border-radius:4px']) : '',
                ],
                'latitude',
                'longitude',
                [
                    'attribute' => 'user_agent',
                    'value' => function ($model) {
                        return Html::encode($model->user_agent);
                    },
                ],
                'created_at:datetime',
            ],
        ]);
        ?>
        </div>
    </div>