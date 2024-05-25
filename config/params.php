<?php

return [
    'supportEmail' => getenv('PARAMS_SUPPORT_EMAIL'),
    'adminEmail' => getenv('PARAMS_ADMIN_EMAIL'),
    'timeZone'=>'Asia/Dhaka',
    'currency'=>'BDT',
    'pageSize'=>50,
    'secretKey'=>getenv('PARAMS_SECRET_KEY'),
    'calendarFormat'=>'dd/m/yyyy',
    'gridviewHeaderColor'=>\kartik\grid\GridView::TYPE_INFO,
];
