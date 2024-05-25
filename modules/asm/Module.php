<?php

namespace app\modules\asm;

use Yii;
use app\modules\asm\models\ModulePermission;
use app\modules\asm\models\Modules;
use app\modules\asm\models\ModulesAction;
use yii\base\BootstrapInterface;
use yii\console\Application;
use yii\helpers\ArrayHelper;

/**
 * asmrole module definition class
 */
class Module extends \yii\base\Module  implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\asm\controllers';
    public $allowedRoute = [];
    public $redis = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->allowedRoute =  require __DIR__ . '/Route.php';
    }

    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $this->controllerNamespace = 'app\modules\asm\commands';
        }
    }
}
