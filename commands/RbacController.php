<?php
namespace app\commands;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacController extends Controller
{
    public function actionScanRoutes()
    {
        $auth = Yii::$app->authManager;
        $controllers = glob(Yii::getAlias('@app/controllers') . '/*Controller.php');

        foreach ($controllers as $controller) {
            $controllerName = basename($controller, '.php');
            $controllerClass = 'app\\controllers\\' . $controllerName;
            $reflector = new \ReflectionClass($controllerClass);
            $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (strpos($method->name, 'action') === 0) {
                    $route = strtolower(str_replace('Controller', '', $controllerName)) . '/' . strtolower(str_replace('action', '', $method->name));
                    $permission = $auth->createPermission($route);
                    $permission->description = "Access to $route";
                    if ($auth->getPermission($route) === null) {
                        $auth->add($permission);
                        Console::output("Added route: $route");
                    }
                }
            }
        }
    }
}
