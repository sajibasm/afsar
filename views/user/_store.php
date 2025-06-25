<?php

use app\models\UserOutlet;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use \app\models\Outlet;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserOutlet */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'User Store');
?>

<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <?php $form = ActiveForm::begin(['id' => 'formAjaxSellCreate']); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'outlet')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Outlet::findAll(['status' => 1, 'type' => 'Outlet']), 'outletId', 'name'),
                    'options' => [
                        'value' => ArrayHelper::getColumn(
                            UserOutlet::find()->where(['userId' => Yii::$app->user->id])
                                ->select(['outletId'])->asArray()->all(), 'outletId'),
                        'placeholder' => 'Select Your Store ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <div class="form-group text-left" style="margin-top: 25px;">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-info', 'style' => 'margin-right:10px;']) ?>
                    <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
