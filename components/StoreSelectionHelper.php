<?php
namespace app\components;

use Yii;
use app\models\Outlet;
use app\models\SalesDraft;

class StoreSelectionHelper
{
    /**
     * Generic Store Selection Handler
     *
     * @param string $redirectAction The action to redirect to (e.g., 'create')
     * @param string $scenario Optional model scenario (default: 'store')
     * @return int|\yii\web\Response|string Returns store ID (int), Response (redirect), or string (rendered view)
     */
    public static function handleStoreSelection($redirectAction, $scenario = 'store')
    {
        $storeIdEncrypted = Yii::$app->request->get('store');
        $storeId = $storeIdEncrypted ? Utility::decrypt($storeIdEncrypted) : null;

        if (empty($storeId) || !is_numeric($storeId)) {

            $model = new Outlet();
            $model->setScenario($scenario);

            $userAssignedStores = StoreUtility::getUserStores();

            if (count($userAssignedStores) > 1) {
                if (Yii::$app->request->isPost) {
                    $model->load(Yii::$app->request->post());
                    if (!empty($model->outletId)) {
                        SalesDraft::deleteAll(['user_id' => Yii::$app->user->id]);
                        return Yii::$app->controller->redirect([$redirectAction, 'store' => Utility::encrypt($model->outletId)]);
                    }
                    $model->addError('outletId', 'Please select an outlet');
                }
            } else {
                SalesDraft::deleteAll(['user_id' => Yii::$app->user->id]);
                $firstStoreId = array_key_first($userAssignedStores);
                return Yii::$app->controller->redirect([$redirectAction, 'store' => Utility::encrypt($firstStoreId)]);
            }

            return Yii::$app->controller->render('/outlet/_store', [  // Fixed view
                'model' => $model
            ]);
        }

        return (int) $storeId;
    }
}
