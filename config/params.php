<?php

return [
    'bsDependencyEnabled' => false, // this will not load Bootstrap CSS and JS for all Krajee extensions
    'supportEmail' => getenv('PARAMS_SUPPORT_EMAIL'),
    'adminEmail' => getenv('PARAMS_ADMIN_EMAIL'),
    'timeZone'=>'Asia/Dhaka',
    'currency'=>'BDT',
    'pageSize'=>50,
    'secretKey'=>getenv('PARAMS_SECRET_KEY'),
    'calendarFormat'=>'dd/m/yyyy',
    'gridviewHeaderColor'=>\kartik\grid\GridView::TYPE_INFO,
];
