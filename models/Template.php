<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "template".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $subject
 * @property string $tags
 * @property string $body
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template';
    }

    public function beforeSave($insert)
    {
        foreach ($this->attributes as $attribute => $value) {
            if (is_string($value)) {
                $this->$attribute = trim($value);
            }
        }
        return parent::beforeSave($insert);
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'unique', 'targetAttribute' => ['name', 'type']],
            [['type', 'tags', 'body'], 'string'],
            [['name', 'subject', 'tags', 'body'], 'required'],
            [['name', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'subject' => 'Subject',
            'tags' => 'Tags',
            'body' => 'Body',
        ];
    }

}
