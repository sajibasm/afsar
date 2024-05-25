<?php
/**
 * Created by PhpStorm.
 * User: asmsajib
 * Date: 8/17/16
 * Time: 2:25 PM
 */
namespace app\components;

use yii\base\Component;

class Hello extends Component
{
    const EVENT_HELLO = 'hello';

    public function init()
    {

    }

    public function bar()
    {
        echo 'mail sent to admin';
        //$this->trigger(self::EVENT_HELLO);
    }
}
