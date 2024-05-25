<?php

use kartik\checkbox\CheckboxX;
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
                'id'=>'notification',
            ]) ?>

            <div class="panel-body">
                <div class="transport-form">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo '<label class="cbx-label" for="s_1">Email (To send email with invoice attachment)</label>';
                            echo CheckboxX::widget([
                                'name'=>'Sales[email]',
                                'options'=>['id'=>'s_1'],
                                'pluginOptions'=>['threeState'=>false]
                            ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            echo '<label class="cbx-label" for="s_1">SMS (To send sms notification)</label>';
                            echo CheckboxX::widget([
                                'name'=>'Sales[sms]',
                                'options'=>['id'=>'s_2'],
                                'pluginOptions'=>['threeState'=>false]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer">
                <div class="modal-footer">
                    <?= Html::submitButton(Yii::t('app', 'Send'), ['class'=>'btn btn-primary']) ?>
                    <?= Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
</div>


