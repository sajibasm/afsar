<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mdm\admin\models\Menu;
use yii\helpers\Json;
use mdm\admin\AutocompleteAsset;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\Menu */
/* @var $form yii\widgets\ActiveForm */
AutocompleteAsset::register($this);
$opts = Json::htmlEncode([
        'menus' => Menu::getMenuSource(),
        'routes' => Menu::getSavedRoutes(),
    ]);

?>

<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <div class="menu-form">

        <?php $form = ActiveForm::begin(['id' => 'formAjaxSellCreate']); ?>
            <?= Html::activeHiddenInput($model, 'parent', ['id' => 'parent_id']); ?>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>

                <?php
                $menus = Menu::find()->select(['id', 'name', 'parent'])->all();
                $menuList = [];

                foreach ($menus as $menu) {
                    $label = $menu->name;
                    if ($menu->parent) {
                        $label = $menu->parent->name . ' → ' . $menu->name;
                    }
                    $menuList[$menu->id] = $label;
                }

                echo $form->field($model, 'parent')->widget(Select2::class, [
                    'data' => $menuList,
                    'options' => [
                        'placeholder' => 'Select Parent Menu...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
                ?>

                <?= $form->field($model, 'route')->textInput(['id' => 'route']) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'order')->input('number') ?>

                <?= $form->field($model, 'data')->textarea(['rows' => 4]) ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group text-right" style="margin-top: 25px;">
                    <?=
                    Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), ['class' => $model->isNewRecord
                        ? 'btn btn-info' : 'btn btn-primary'])
                    ?>
                    <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

