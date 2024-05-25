<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%brand_new}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $status
 * @property string $extra
 */
class BrandNew extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand_new}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'string'],
            [['extra'], 'required'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'extra' => Yii::t('app', 'Extra'),
        ];
    }
}
