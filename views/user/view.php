<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
$this->title = $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-info">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <div class="row">
            <div class="col-md-12">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'first_name',
                        'last_name',
                        'username',
                        'email',

                        [
                            'attribute' => 'user_image',
                            'format' => ['image', ['width' => '80', 'height' => '80']],
                            'value' => function ($model) {
                                return $model->user_image ? Url::to('@web/uploads/' . $model->user_image, true) : null;
                            },
                        ],

                        [
                            'label' => 'Store(s)',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $badges = '';
                                foreach ($model->userOutletDetail as $outletModel) {
                                    $storeName = $outletModel->outletDetail->name ?? 'N/A';
                                    $badges .= "<span class='badge bg-blue-active' style='margin-right:5px;'>{$storeName}</span>";
                                }
                                return $badges ?: "<span class='badge bg-dark'>No Store</span>";
                            },
                        ],

                        [
                            'label' => '2FA Status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->is_2fa_enabled
                                    ? '<span class="badge bg-green-active" title="2FA Enabled"><i class="fas fa-lock"></i></span>'
                                    : '<span class="badge bg-yellow-active" title="2FA Disabled"><i class="fas fa-unlock-alt"></i></span>';
                            },
                        ],

                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $labels = \app\components\ConstrainUtility::USER_STATUS_LIST;
                                $label = $labels[$model->status] ?? 'Unknown';

                                switch ($model->status) {
                                    case 10:
                                        $class = 'bg-green';
                                        break;
                                    case 1:
                                        $class = 'bg-secondary';
                                        break;
                                    case 2:
                                        $class = 'bg-danger';
                                        break;
                                    default:
                                        $class = 'bg-dark';
                                }

                                return "<span class='badge {$class}'>{$label}</span>";
                            },
                        ],

                        'created_at',
                        'updated_at',
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
