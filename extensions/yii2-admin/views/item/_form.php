<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mdm\admin\components\RouteRule;
use mdm\admin\AutocompleteAsset;
use yii\helpers\Json;
use mdm\admin\components\Configs;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$rules = Configs::authManager()->getRules();
unset($rules[RouteRule::RULE_NAME]);
$source = Json::htmlEncode(array_keys($rules));

$js = <<<JS
    $('#rule_name').autocomplete({
        source: $source,
    });
JS;
AutocompleteAsset::register($this);
$this->registerJs($js);
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

                <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'ruleName')->textInput(['id' => 'rule_name']) ?>

                <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group text-right" style="margin-top: 25px;">

                <?php
                echo Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), [
                    'class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary',
                    'name' => 'submit-button'])
                ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>

                </div>
            </div>
        </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

