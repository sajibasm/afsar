<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%app_settings}}".
 *
 * @property integer $id
 * @property string $app_options
 * @property string $app_values
 * @property string $type
 * @property string $readOnly
 * @property string $status
 */
class AppSettings extends \yii\db\ActiveRecord
{

    const ACTIVE_STATUS = 'Active';
    const INACTIVE_STATUS = 'Inctive';

    const FILE_TYPE = 'file';
    const RAW_TYPE = 'raw';
    const JSON_TYPE = 'json';
    const BOOl_TYPE = 'bool';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_options', 'app_values', 'type'], 'required'],
            [['app_values', 'readOnly', 'status', 'type'], 'string'],
            [['app_options'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'app_options' => Yii::t('app', 'Option'),
            'app_values' => Yii::t('app', 'Value'),
            'type' => Yii::t('app', 'Type'),
            'readOnly' => Yii::t('app', 'Read Only'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
