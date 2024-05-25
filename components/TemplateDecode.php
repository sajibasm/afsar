<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\components;

use app\models\Template;
use Yii;

class TemplateDecode
{

    /**
     * @param $model
     * @param array $tags
     */
    public static function decodeByTags(Template $model, $tags = [])
    {
        $message =  $model->body;
        foreach ($tags as $tag=>$value){
            $message = str_replace("#$tag#", $value, $message);;
        }
        return $message;
    }
}