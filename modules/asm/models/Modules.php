<?php
namespace app\modules\asm\models;

use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%modules}}".
 *
 * @property int $id
 * @property string $code
 * @property string|null $name
 * @property string $controller
 * @property string $icon
 * @property int $priority
 * @property int|null $active
 */
class Modules extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%modules}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller', 'icon'], 'required'],
            [['active'], 'integer'],
            [['code'], 'string', 'max' => 128],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['controller'], 'string', 'max' => 50],
            [['priority'], 'integer'],
            [['icon'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'controller' => Yii::t('app', 'Controller'),
            'icon' => Yii::t('app', 'Icon'),
            'priority' => Yii::t('app', 'Priority'),
            'active' => Yii::t('app', 'Active'),
        ];

    }

}
