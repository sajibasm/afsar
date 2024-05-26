<?php

use app\components\EmployeeUtility;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EmployeeDesignation */
/* @var $form yii\widgets\ActiveForm */
?>



            <?php $form = ActiveForm::begin() ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?php
                    echo $form->field($model, 'status')->widget(Select2::classname(), [
                        'theme'=>Select2::THEME_DEFAULT,
                        'data' =>EmployeeUtility::getEmployeeStatus(),
                        'options' => [
                            'placeholder' => 'Select status'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>


            <div class="panel-footer">
                <div class="modal-footer">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    <?= Html::a('Back', ['index'], ['class' => 'btn btn-default'])?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>


