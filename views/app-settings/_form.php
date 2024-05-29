<?php

use app\models\AppSettings;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AppSettings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-settings-form">



        <?php $form = ActiveForm::begin() ?>


        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'app_options')->textInput(['maxlength' => true, 'readOnly'=>$model->isNewRecord?false:true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <?php if($model->type==AppSettings::BOOl_TYPE):?>
                <?php
                    echo $form->field($model, 'app_values')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' => ['true'=>'True', 'false'=>'False'],
                        'options' => ['placeholder' => 'Select One'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                ?>
            <?php elseif ($model->type==AppSettings::RAW_TYPE || $model->type==AppSettings::JSON_TYPE):?>
                <?= $form->field($model, 'app_values')->textarea(['maxlength' => true]) ?>
            <?php elseif ($model->type==AppSettings::FILE_TYPE):?>
                <?= $form->field($model, 'app_values')->fileInput() ?>
            <?php endif;?>
        </div>
        </div>

        <div class="panel-footer">
            <div class="modal-footer">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

</div>
