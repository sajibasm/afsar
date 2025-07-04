<?php

    use kartik\widgets\Select2;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Branch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branch-form">
    <?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'bank_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map($model->getBankList(), 'bank_id', 'bank_name'),
                'options' => ['placeholder' => 'Select a Bank'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Bank');
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'branch_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="panel-footer">
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center">
                    <?= \app\components\ButtonHelper::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
