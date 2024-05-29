<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;
use app\models\Outlet;
use kartik\widgets\DatePicker;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class DateWidget {

    public static function dateRange($model, $form, $label, $startAtt, $endAtt, $isToday = true){
        echo '<label class="control-label">'.$label.'</label>';
        echo  DatePicker::widget([
            //'disabled' => true,
            'model' => $model,
            'attribute' => $startAtt,
            'attribute2' => $endAtt,
            'options' => ['placeholder' => 'Start date'],
            'options2' => ['placeholder' => 'End date'],
            'type' => DatePicker::TYPE_RANGE,
            'form' => $form,
            'readonly' => true,
            'pluginOptions' => [
                //'orientation' => 'top right',
                'todayHighlight' => $isToday,
                'format' => SystemSettings::calenderDateFormat(),
                'autoclose' => true,
            ]
        ]);
    }

}
