<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use aryelds\sweetalert\SweetAlert;
use kartik\widgets\Growl;
use Yii;
use yii\helpers\Html;

class FlashMessage
{

    public function FlashMessage()
    {

    }

    public static function setMessage($message, $title, $type = 'error')
    {
        Yii::$app->getSession()->setFlash('notificationMessage', [
            'text' => $message,
            'title' => $title,
            'type' => $type,
            'showConfirmButton' => false
        ]);
    }


    public static function getMessage()
    {
//        if( Yii::$app->session->hasFlash('notificationMessage')){
//            $data = Yii::$app->session->getFlash('notificationMessage');
//            return " n.show().setText('".$data['text']."').setType('".$data['type']."').setTheme('sunset').setTimeout(3000).closeWith(['click']);";
//        }

        if( Yii::$app->session->hasFlash('notificationMessage')){
            $message = Yii::$app->session->getFlash('notificationMessage');
            echo SweetAlert::widget([
                'options' => [
                    'title' => (!empty($message['title'])) ? Html::encode($message['title']) : 'Title Not Set!',
                    'text' => (!empty($message['text'])) ? Html::encode($message['text']) : 'Text Not Set!',
                    'type' => (!empty($message['type'])) ? $message['type'] : SweetAlert::TYPE_INFO,
                    'timer' => (!empty($message['timer'])) ? $message['timer'] : 5000,
                    'showConfirmButton' =>  (!empty($message['showConfirmButton'])) ? $message['showConfirmButton'] : true
                ]
            ]);
        }
    }

}