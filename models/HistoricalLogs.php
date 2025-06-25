<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%historical_logs}}".
 *
 * @property int $id
 * @property string $model_name
 * @property int $model_id
 * @property string $action
 * @property string $user_action
 * @property string|null $changed_attributes
 * @property int|null $performed_by
 * @property string|null $performed_at
 * @property string|null $ip_address
 * @property string|null $user_agent
 */
class HistoricalLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%historical_logs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_name', 'model_id', 'action'], 'required'],
            [['model_id', 'performed_by'], 'integer'],
            [['changed_attributes', 'user_agent'], 'string'],
            [['performed_at'], 'safe'],
            [['model_name', 'ip_address', 'user_action'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model_name' => Yii::t('app', 'Model Name'),
            'model_id' => Yii::t('app', 'Model ID'),
            'action' => Yii::t('app', 'Action'),
            'user_action' => Yii::t('app', 'User Action'),
            'changed_attributes' => Yii::t('app', 'Changed Attributes'),
            'performed_by' => Yii::t('app', 'Performed By'),
            'performed_at' => Yii::t('app', 'Performed At'),
            'ip_address' => Yii::t('app', 'Ip Address'),
            'user_agent' => Yii::t('app', 'User Agent'),
        ];
    }

    public static function logChange($params)
    {
        $log = new self();
        $log->model_name = $params['model'];
        $log->model_id = $params['model_id'];
        $log->action = $params['action'];

        $model = $params['model_instance'] ?? null;

        if ($params['action'] === 'update' && $model !== null) {
            $diffs = [];
            foreach ($params['attributes'] as $attr => $oldValue) {
                $newValue = $model->getAttribute($attr);
                if ($oldValue != $newValue) {
                    $diffs[$attr] = ['old' => $oldValue, 'new' => $newValue];
                }
            }
            $log->changed_attributes = json_encode($diffs);
        } else {
            $log->changed_attributes = json_encode($params['attributes']);
        }

        $log->performed_by = Yii::$app->user->id ?? null;
        $log->ip_address = Yii::$app->request->userIP ?? null;
        $log->user_agent = Yii::$app->request->userAgent ?? null;
        $log->performed_at = date('Y-m-d H:i:s');

        $log->save(false);
    }
}
