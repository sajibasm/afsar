<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this  yii\web\View */
/* @var $model mdm\admin\models\BizRule */
/* @var $form ActiveForm */
?>



<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <?php $form = ActiveForm::begin(['id' => 'formAjaxSellCreate']); ?>

        <div class="auth-item-form">

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'className')->textInput() ?>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-right" style="margin-top: 25px;">
                        <?php
                        echo Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), [
                            'class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary'])
                        ?>
                        <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
