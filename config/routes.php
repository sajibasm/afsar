<?php

return [
    //'*',

    'site/*',
    'api/*',

    'admin/user/login',
    'admin/*',
    'cron-job/*',
    'reports/*',

    //For Sales Feature
    'sales/get-brand-list-by-item',
    'sales/get-size-list-by-brand',
    'sales/get-product-price',
    'sales/check-available-product',
    'sales/customer-details',
    'sales/get-branch',
    'sales/draft-update',
    'sales/invoice-item-delete',

    //For Stock Feature

    'product-stock/get-item-by-brand',
    'product-stock/get-brand-list-by-item',
    'product-stock/get-size-list-by-brand',
    'product-stock/item-update',
    'product-stock/stock-delete',

    //For Return Common
    'sales-return/items',
    'sales-return/items-remove',

];


