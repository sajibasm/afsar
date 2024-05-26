<?php

use app\components\CustomerUtility;
use app\models\Client;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MarketBook */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <div class="row">

            <div class="col-md-6">
                <?php
                echo $form->field($model, 'client_id')->widget(Select2::classname(), [
                    'theme'=>Select2::THEME_DEFAULT,
                    'data' => ArrayHelper::map(CustomerUtility::getCustomerList(Client::CUSTOMER_TYPE_REGULAR), 'client_id', 'client_name'),
                    'options' => [
                        'id'=>'item_id',
                        'placeholder' => 'Select a Items'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(false);
                ?>
            </div>

            <div class="col-md-6">
                <?= Html::submitButton(Yii::t('app', 'Next') , ['class' =>'btn btn-success ']) ?>
            </div>
        </div>


    <?php ActiveForm::end(); ?>
