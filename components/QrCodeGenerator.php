<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */

namespace app\components;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Yii;

class QrCodeGenerator
{
    public static function generate($text)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(100),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($text);
        // Remove XML declaration
        $svg = preg_replace('/<\?xml.*\?>/', '', $svg);
        return $svg;
    }
}