<?php

use app\models\Transport;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

use yii\web\View;
use yii\widgets\Pjax;

$this->registerJsFile('@web/lib/js/sales.js', ['depends'=> JqueryAsset::className()]);
/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-form">
        <div class="box-body" id="sales_transport">
            <?php $form = ActiveForm::begin([
                'id'=>'transport',
            ]) ?>

            <div class="panel-body">
                <div class="transport-form">

                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo $form->field($model, 'transport_id')->widget(Select2::classname(), [
                                'theme'=>Select2::THEME_DEFAULT,
                                'data' => ArrayHelper::map(Transport::find()->all(), 'transport_id', 'transport_name'),
                                'options' => [
                                    'placeholder' => 'Select ... '
                                ]
                            ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'tracking_number')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer">
                <div class="modal-footer">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    <?= Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
</div>


