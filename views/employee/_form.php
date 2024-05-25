<?php

use app\components\EmployeeUtility;
use dosamigos\datepicker\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">



        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

            <div class="employee-form">

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-4">
                        <?php
                        echo $form->field($model, 'designation')->widget(Select2::classname(), [
                            'theme'=>Select2::THEME_DEFAULT,
                            'data' => ArrayHelper::map(EmployeeUtility::getEmployeeDesignationList(), 'id', 'name'),
                            'options' => ['placeholder' => 'Select Position'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'dob')->widget(
                            DatePicker::className(), [
                            'template' => '{addon}{input}',
                            'clientOptions' => [
                                'autoclose' => true,
                                'endDate'=>EmployeeUtility::getEndDateOfDOB(),
                                'format' => 'yyyy-m-dd'
                            ]
                        ]);?>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4">
                        <?php
                            if(!$model->getIsNewRecord()){
                                echo '<div class="form-group field-size-size_image">
                                <img width="100px" height="100px" src="'.$model->getImageUrl(false).'">
                                </div>';
                            }
                        ?>
                        <?= $form->field($model, 'imageFile')->fileInput() ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'contact_number')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-4">
                        <?= $form->field($model, 'salary')->textInput() ?>
                    </div>

                    <div class="col-md-4">
                        <?= $form->field($model, 'joining_date')->widget(
                            DatePicker::className(), [
                            'template' => '{addon}{input}',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-m-dd'
                            ]
                        ]);?>
                    </div>
                    <div class="col-md-4">
                        <?php
                        echo $form->field($model, 'status')->widget(Select2::classname(), [
                                'theme'=>Select2::THEME_DEFAULT,
                            'data' => EmployeeUtility::getEmployeeStatus(),
                            'options' => ['placeholder' => 'Status'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>


                </div>


                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'present_address')->textarea(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'permanent_address')->textarea(['maxlength' => true]) ?>
                    </div>

                    <div class="col-md-4">
                        <?= $form->field($model, 'remarks')->textarea(['maxlength' => true]) ?>
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

