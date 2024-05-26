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

$this->title = Yii::t('app', 'Verify');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Return'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="panel panel-primary">

    <div class="panel-heading">Verify Your Invoice</div>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="panel-body">

        <?= $form->errorSummary($model); ?>

        <div class="row">

            <div class="col-md-4">


                <?php

                if (OutletUtility::numberOfOutletByUser() > 1) {
                    echo $form->field($model, 'outletId')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_DEFAULT,
                        'data' => OutletUtility::getUserOutlet(),
                        'options' => [
                            'id' => 'outlet_id',
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

            <div class="col-md-4">
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

            <div class="col-md-4">
                <?= $form->field($model, 'sales_id')->textInput(['maxlength' => true]) ?>
            </div>

        </div>

    </div>

    <div class="panel-footer">

        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Next') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
</div>
