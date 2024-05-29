<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%backup}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $size
 * @property string $note
 * @property string $status
 * @property string $date
 */
class Backup extends \yii\db\ActiveRecord
{

    const STATUS_EXPORT = 'Exported';
    const STATUS_IMPORT = 'Imported';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backup}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'date'], 'required'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['size'], 'string', 'max' => 20],
            [['note'], 'string', 'max' => 300],
            [['status'], 'string', 'max' => 50],
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
            'size' => Yii::t('app', 'Size'),
            'note' => Yii::t('app', 'Note'),
            'status' => Yii::t('app', 'Status'),
            'date' => Yii::t('app', 'Date'),
        ];
    }
}
