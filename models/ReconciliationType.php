<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%receoncliation_type}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $show_invoice
 * @property string $status
 */
class ReconciliationType extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    const VISIBLE_ON_INVOICE_YES = 'yes';
    const INVISIBLE_ON_INVOICE_NO = 'no';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reconciliation_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status', 'show_invoice'], 'required'],
            [['name'] ,'unique'],
            [['name', 'status'], 'trim'],
            [['status'], 'string'],
            ['status', 'default', 'value' => 'Active'],
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
            'show_invoice' => Yii::t('app', 'Show On Invoice'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
