<?php

use app\components\CustomerUtility;
use app\components\OutletUtility;
use app\models\Client;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\SalesReturn */

$this->title = Yii::t('app', 'Verify Invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Return'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="box box-success">
    <div class="box-header with-border text-center">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>

    <div class="box-body p-0">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <div class="row">
            <div class="col-md-3">
                <?php

                if (OutletUtility::numberOfOutletByUser() > 1) {
                    echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_DEFAULT,
                        'data' => OutletUtility::getUserOutlet(),
                        'options' => [
                            'id' => 'outlet_id',
                            'placeholder' => 'Select Store'
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
                            'placeholder' => 'Select Store '
                        ]
                    ]);

                }
                ?>
            </div>

            <div class="col-md-3">
                <?php

                if (OutletUtility::numberOfOutletByUser() > 1) {
                    echo $form->field($model, 'client_id')->widget(DepDrop::classname(), [
                        //'theme'=>Select2::THEME_DEFAULT,
                        'type' => DepDrop::TYPE_SELECT2,
                        'select2Options' => ['pluginOptions' => ['allowClear' => true], 'theme' => Select2::THEME_DEFAULT],
                        //'options' => ['id' => 'brand_id'],
                        'pluginOptions' => [
                            'depends' => ['outlet_id'],
                            'placeholder' => 'Select Customer',
                            'url' => Url::to(['/client/by-outlet'])
                        ]
                    ]);
                } else {
                    echo $form->field($model, 'client_id')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_DEFAULT,
                        'data' => CustomerUtility::getCustomerWithAddressList(null, 'client_name asc', true, $model->outletId),
                        'options' => [
                            'placeholder' => 'Select a customer '
                        ]
                    ]);
                }
                ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'sales_id')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-3">
                <div class="form-group text-left" style="margin-top: 25px;">
                    <?= Html::submitButton('Confirm', ['class' => 'btn btn-info', 'style' => 'margin-right:10px;']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>