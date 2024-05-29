<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;

use app\models\ReturnDraft;
use app\models\SalesDraft;
use app\models\User;
use app\models\UserRole;
use yii\helpers\ArrayHelper;

class ConstrainUtility
{
    const  ROLE_ACTIVE_STATUS = 1;
    const  ROLE_INACTIVE_STATUS = 0;

    const  ROLE_ACTIVE_STATUS_LABEL = 'Active';
    const  ROLE_INACTIVE_STATUS_LABEL = 'Inactive';

    const ROLE_STATUS_LIST = [
        self::ROLE_ACTIVE_STATUS =>self::ROLE_ACTIVE_STATUS_LABEL,
        self::ROLE_INACTIVE_STATUS =>self::ROLE_INACTIVE_STATUS_LABEL,
    ];


    const  EMPLOYEE_ACTIVE_STATUS = 1;
    const  EMPLOYEE_INACTIVE_STATUS = 0;

    const  EMPLOYEE_ACTIVE_STATUS_LABEL = 'Active';
    const  EMPLOYEE_INACTIVE_STATUS_LABEL = 'Inactive';

    const EMPLOYEE_STATUS_LIST = [
        self::EMPLOYEE_ACTIVE_STATUS =>self::EMPLOYEE_ACTIVE_STATUS_LABEL,
        self::EMPLOYEE_INACTIVE_STATUS =>self::EMPLOYEE_INACTIVE_STATUS_LABEL,
    ];


    const  USER_ACTIVE_STATUS = 10;
    const  USER_INACTIVE_STATUS = 1;

    const  USER_ACTIVE_STATUS_LABEL = 'Active';
    const  USER_INACTIVE_STATUS_LABEL = 'Inactive';

    const USER_STATUS_LIST = [
        self::USER_ACTIVE_STATUS =>self::USER_ACTIVE_STATUS_LABEL,
        self::USER_INACTIVE_STATUS =>self::USER_INACTIVE_STATUS_LABEL,
    ];
}