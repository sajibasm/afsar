<?php

    use app\components\ProductUtility;
    use kartik\widgets\Select2;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model app\models\Brand */
    /* @var $form yii\widgets\ActiveForm */
?>



    <?php $form = ActiveForm::begin() ?>


    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'item_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ProductUtility::getItemList(), 'item_id', 'item_name'),
                'options' => ['placeholder' => 'Select a Items'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'brand_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ProductUtility::getBrandListMap(), 'id', 'name'),
                'options' => ['placeholder' => 'Select a Brand'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'brand_status')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => [ 'Active' => 'Active', 'Inactive' => 'Inactive', ],
                'options' => ['placeholder' => 'Select Status'],
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
