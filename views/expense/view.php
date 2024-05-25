<?php

use app\components\Utility;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Expense */

$this->title = $model->expense_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-view">

    <div class="row">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'expense_id',
                'user.username',
                'expenseType.expense_type_name',
                'type',
                'expense_amount',
                'expense_remarks',
                'status',
                'created_at',
            ],
        ]) ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Approved'), ['class' => 'btn btn-primary', 'id'=>'approveConfirmation', 'data-view'=>'expensePjaxGridView',  'data-link'=>Url::to(['approved','id'=>Utility::encrypt($model->expense_id)])]) ?>
                <?= Html::a('Close', ['#'], ['class' => 'btn btn-default', 'id'=>'approveConfirmationClose',]) ?>
            </div>
        </div>
    </div>

</div>
