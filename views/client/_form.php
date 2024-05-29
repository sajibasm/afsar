<?php

use app\components\CustomerUtility;
use app\components\OutletUtility;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin() ?>

<div class="row">
    <div class="col-md-12">
        <?php

        if (OutletUtility::numberOfOutletByUser() > 1) {
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'options' => [
                    //'id' => 'outlet_id',
                    'placeholder' => 'Outlet'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        } else {
            echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                'theme' => Select2::THEME_DEFAULT,
                'data' => OutletUtility::getUserOutlet(),
                'pluginOptions' => [
                    'disabled' => true
                ],
                'options' => [
                    'placeholder' => 'Outlet '
                ]
            ]);

        }
        ?>


    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'client_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'client_contact_number')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
</div>


<div class="row">
    <div class="col-md-4">
        <?php
        echo $form->field($model, 'client_city')->widget(Select2::classname(), [
            'theme' => Select2::THEME_DEFAULT,
            'data' => ArrayHelper::map(CustomerUtility::getCityList(), 'city_id', 'city_name'),
            'options' => ['placeholder' => 'Select a City'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'client_address1')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'client_address2')->textInput(['maxlength' => true]) ?>
    </div>
</div>


<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'client_contact_person')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'client_contact_person_number')->textInput(['maxlength' => true]) ?>

    </div>
    <div class="col-md-4">
        <?php
        echo $form->field($model, 'client_type')->widget(Select2::classname(), [
            'theme' => Select2::THEME_DEFAULT,
            'data' => ['regular' => 'Regular', 'irregular' => 'Irregular',],
            'options' => ['placeholder' => 'Select Type'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>


<div class="panel-footer">
    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-info']) ?>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

