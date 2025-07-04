<?php


    use app\components\CommonUtility;
    use kartik\widgets\Select2;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Buyer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="buyer-form">

    <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'city')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(CommonUtility::getCityList(), 'city_id', 'city_name'),
                'options' => ['placeholder' => 'Select a City'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'contact_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'contact_person_number')->textInput(['maxlength' => true]) ?>
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
