<?php

return [
    '*'=>[],

    'client'=>['by-outlet'],

    'site'=>[
        '*',
        'login',
        'chart',
        'daily-summery',
        'index',
        'sales-growth',
        'analytics',
        'permission'
    ],

    'sales' => [
        'index',
        'outlet',
        'get-brand-list-by-item',
        'get-size-list-by-brand',
        'get-product-price',
        'check-available-product',
        'customer-details',
        'draft-update',
        'invoice-item-update-restore',
        'invoice-item-update-delete',
        'invoice-item-delete',
        'cancel-sales-invoice',
        'cancel-update-invoice',
        'delete-invoice',
        'remove-invoice',
        'restore'
    ],

    'product-stock' => [
        'outlet',
        'received-view',
        'received-approved',
        'restore-approved',
        'get-item-by-brand',
        'get-brand-list-by-item',
        'get-size-list-by-brand',
        'get-product-price',
        'existing-price',
        'stock-delete-all',
        'discard',
        'stock-delete-all'
    ],

    'product-stock-movement' => [
        'get-item-by-brand',
        'get-brand-list-by-item',
        'get-size-list-by-brand',
        'product-details-by-size-id',
        'get-product-price'
    ],


];
