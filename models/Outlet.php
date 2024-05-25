<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "outlet".
 *
 * @property int $outletId
 * @property string $outletCode
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $logo
 * @property string $logoWaterMark
 * @property string $contactNumber
 * @property string $email
 * @property string $type
 * @property string $priority
 * @property int $status
 */
class Outlet extends \yii\db\ActiveRecord
{

    const TYPE_WAREHOUSE = 'Warehouse';
    const TYPE_OUTLET = 'Outlet';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'outlet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['outletCode', 'name', 'address1', 'address2', 'logo', 'logoWaterMark', 'contactNumber', 'email', 'type'], 'required'],
            [['status', 'priority'], 'integer'],
            [['outletCode', 'logo', 'logoWaterMark', 'email'], 'string', 'max' => 100],
            [['name', 'address1', 'address2'], 'string', 'max' => 255],
            [['contactNumber'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'outletId' => 'Outlet ID',
            'outletCode' => 'Outlet Code',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'logo' => 'Logo',
            'logoWaterMark' => 'Logo Water Mark',
            'contactNumber' => 'Contact Number',
            'email' => 'Email',
            'status' => 'Status',
            'priority' => 'Priority',
            'type' => 'Type',
        ];
    }
}
