<?php

use mdm\admin\components\Helper;
use mdm\admin\components\MenuHelper;
use dmstr\widgets\Menu;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= Yii::getAlias('@web') . '/images/user.png' ?>" class="img-circle"
                     alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= isset(Yii::$app->user->identity) ? ucwords(Yii::$app->user->identity->username) : ''; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>


        <?php


       $menuItems = [
           ['label' => 'Options', 'options' => ['class' => 'header']],

           [
               'label' => 'Profile',
               'icon' => 'fas fa-user-circle',
               'url' => ['/admin/user/profile'],
           ],

           ['label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => ['/site/index']],

           [
               'label' => 'Sell',
               'icon' => 'fas fa-cash-register',
               'url' => '#',
               'items' => [
                   ['label' => 'Sell', 'icon' => 'fas fa-cash-register', 'url' => ['/sales/outlet']],
                   ['label' => 'Sales History', 'icon' => 'fas fa-receipt', 'url' => ['/sales/index']],
               ]
           ],

           [
               'label' => 'Return & Service',
               'icon' => 'fas fa-tools',
               'url' => '#',
               'items' => [
                   [
                       'label' => 'Return',
                       'icon' => 'fas fa-undo-alt',
                       'url' => ['/sales-return/index']
                   ],
                   [
                       'label' => 'Product',
                       'icon' => 'fas fa-box-open',
                       'url' => ['/sales-return/verify-repair']
                   ],
                   [
                       'label' => 'Service',
                       'icon' => 'fas fa-wrench',
                       'url' => ['/sales-return/verify-repair']
                   ],
               ]
           ]
           ,

           [
               'label' => 'Marketbook',
               'icon' => 'fas fa-book',
               'url' => '#',
               'items' => [
                   ['label' => 'Create Marketbook', 'icon' => 'fas fa-plus-square', 'url' => ['/market-book/create']],
                   ['label' => 'Marketbook List', 'icon' => 'fas fa-book-open', 'url' => ['/market-book/index']],
               ]
           ],

           [
               'label' => 'Stock',
               'icon' => 'fas fa-boxes',
               'url' => '',
               'items' => [
                   [
                       'label' => 'Inventory',
                       'icon' => 'fas fa-warehouse',
                       'url' => '',
                       'items' => [
                           ['label' => 'New', 'icon' => 'fas fa-plus-circle', 'url' => ['/product-stock/create']],
                           ['label' => 'Transfer', 'icon' => 'fas fa-exchange-alt', 'url' => ['/product-stock/transfer']],
                           ['label' => 'Stock History', 'icon' => 'fas fa-history', 'url' => ['/product-stock/index']],
                           ['label' => 'Items History', 'icon' => 'fas fa-list-alt', 'url' => ['/product-stock/items']],
                       ]
                   ],
                   [
                       'label' => 'Outlet',
                       'icon' => 'fas fa-store',
                       'url' => '',
                       'items' => [
                           ['label' => 'Transfer', 'icon' => 'fas fa-exchange-alt', 'url' => ['/product-stock-movement/outlet']],
                           ['label' => 'Stock History', 'icon' => 'fas fa-history', 'url' => ['/product-stock-outlet/index']],
                           ['label' => 'Stock Statement', 'icon' => 'fas fa-file-alt', 'url' => ['/product-statement-outlet/index']]
                       ]
                   ]
               ]
           ],

           [
               'label' => 'Reports',
               'icon' => 'fas fa-chart-bar',
               'url' => '#',
               'items' => [
                   [
                       'label' => 'Sales',
                       'icon' => 'fas fa-file-invoice-dollar',
                       'url' => '',
                       'items' => [
                           ['label' => 'Sales', 'icon' => 'fas fa-receipt', 'url' => ['/reports/sales']],
                           ['label' => 'Return', 'icon' => 'fas fa-undo', 'url' => ['/reports/return']],
                           ['label' => 'Marketbook', 'icon' => 'fas fa-book', 'url' => ['/reports/market']],
                       ]
                   ],
                   [
                       'label' => 'Stock',
                       'icon' => 'fas fa-cubes',
                       'url' => '',
                       'items' => [
                           ['label' => 'History', 'icon' => 'fas fa-history', 'url' => ['/reports/product-stock']],
                           ['label' => 'Movement', 'icon' => 'fas fa-exchange-alt', 'url' => ['/reports/product-stock-movement']],
                           ['label' => 'Low', 'icon' => 'fas fa-level-down-alt', 'url' => ['/reports/product-stock']],
                       ]
                   ],
                   [
                       'label' => 'Cash',
                       'icon' => 'fas fa-wallet',
                       'url' => '',
                       'items' => [
                           ['label' => 'History', 'icon' => 'fas fa-history', 'url' => ['/reports/cash-book']],
                           ['label' => 'Summery', 'icon' => 'fas fa-file-invoice', 'url' => ['/reports/cash-book-summery']]
                       ]
                   ],
                   [
                       'label' => 'Bank',
                       'icon' => 'fas fa-university',
                       'url' => '',
                       'items' => [
                           ['label' => 'History', 'icon' => 'fas fa-history', 'url' => ['/reports/deposit-book']],
                           ['label' => 'Summery', 'icon' => 'fas fa-file-invoice', 'url' => ['/reports/bank-book-summery']]
                       ]
                   ],
                   [
                       'label' => 'Accounts',
                       'icon' => 'fas fa-user-friends',
                       'url' => '',
                       'items' => [
                           ['label' => 'Withdraw', 'icon' => 'fas fa-arrow-circle-down', 'url' => ['/reports/withdraw']],
                           ['label' => 'Hand Received', 'icon' => 'fas fa-handshake', 'url' => ['/reports/cash-hand-received']],
                       ]
                   ],
                   [
                       'label' => 'Payment',
                       'icon' => 'fas fa-hand-holding-usd',
                       'url' => '',
                       'items' => [
                           ['label' => 'Received', 'icon' => 'fas fa-money-check-alt', 'url' => ['/reports/customer-payment-received']],
                           ['label' => 'Refund', 'icon' => 'fas fa-undo', 'url' => ['/reports/customer-payment-refund']],
                       ]
                   ],
                   [
                       'label' => 'Expense',
                       'icon' => 'fas fa-money-bill-wave',
                       'url' => '',
                       'items' => [
                           ['label' => 'Expense', 'icon' => 'fas fa-file-invoice-dollar', 'url' => ['/reports/expense']],
                           ['label' => 'Warehouse', 'icon' => 'fas fa-warehouse', 'url' => ['/reports/warehouse']],
                           ['label' => 'LC', 'icon' => 'fas fa-file-contract', 'url' => ['/reports/lc']],
                       ]
                   ],
                   [
                       'label' => 'Product',
                       'icon' => 'fas fa-cube',
                       'url' => '',
                       'items' => [
                           ['label' => 'Product', 'icon' => 'fas fa-cube', 'url' => ['/reports/product']],
                           ['label' => 'Brand', 'icon' => 'fas fa-tags', 'url' => ['/reports/product-brand']],
                           ['label' => 'Customer', 'icon' => 'fas fa-user', 'url' => ['/reports/product-customer']],
                       ]
                   ],
               ],
           ],

           [
               'label' => 'Accounts',
               'icon' => 'fas fa-folder-open',
               'url' => '#',
               'items' => [
                   ['label' => 'Withdraw', 'icon' => 'fas fa-arrow-circle-down', 'url' => ['/withdraw/index']],
                   ['label' => 'Hand Received', 'icon' => 'fas fa-handshake', 'url' => ['/cash-hand-received/index']],
               ],
           ],

           [
               'label' => 'Expense',
               'icon' => 'fas fa-shopping-bag',
               'url' => '#',
               'items' => [
                   ['label' => 'LC', 'icon' => 'fas fa-file-contract', 'url' => ['/lc-payment/index']],
                   ['label' => 'Expense', 'icon' => 'fas fa-money-check-alt', 'url' => ['/expense/index']],
                   ['label' => 'Warehouse', 'icon' => 'fas fa-warehouse', 'url' => ['/warehouse-payment/index']],
                   ['label' => 'Reconciliation', 'icon' => 'fas fa-balance-scale', 'url' => ['/bank-reconciliation/index']],
               ],
           ],

           [
               'label' => 'Customer',
               'icon' => 'fas fa-user-friends',
               'url' => '#',
               'items' => [
                   ['label' => 'Customer', 'icon' => 'fas fa-user', 'url' => ['/client/index']],
                   ['label' => 'Payment History', 'icon' => 'fas fa-history', 'url' => ['/client-payment-history/index']],
                   ['label' => 'Payment Details', 'icon' => 'fas fa-info-circle', 'url' => ['/client-payment-details/index']],
                   ['label' => 'Refund', 'icon' => 'fas fa-undo', 'url' => ['/customer-withdraw/index']],
                   ['label' => 'Dues', 'icon' => 'fas fa-exclamation-circle', 'url' => ['/customer-account/dues']],
                   ['label' => 'Invoice', 'icon' => 'fas fa-file-invoice', 'url' => ['/customer-account/index']],
               ],
           ],

           [
               'label' => 'Payroll',
               'icon' => 'fas fa-credit-card',
               'url' => '',
               'items' => [
                   [
                       'label' => 'Salary',
                       'icon' => 'fas fa-dollar-sign',
                       'url' => '',
                       'items' => [
                           ['label' => 'Payslip', 'icon' => 'fas fa-file-alt', 'url' => ['/salary-history/payroll-slip']],
                           ['label' => 'Salary', 'icon' => 'fas fa-money-bill-wave', 'url' => ['/salary-history/salary']],
                           ['label' => 'Advance', 'icon' => 'fas fa-hand-holding-usd', 'url' => ['/salary-history/advance-salary']],
                       ]
                   ],
                   [
                       'label' => 'Employee',
                       'icon' => 'fas fa-user-circle',
                       'url' => '',
                       'items' => [
                           ['label' => 'Role', 'icon' => 'fas fa-user-tag', 'url' => ['/employee-designation/index']],
                           ['label' => 'Employee', 'icon' => 'fas fa-users', 'url' => ['/employee/index']],
                       ]
                   ]
               ]
           ],

           [
               'label' => 'User Access',
               'icon' => 'fas fa-universal-access',
               'url' => '',
               'items' => [
                   ['label' => 'User', 'icon' => 'fas fa-user', 'url' => ['/user/index']],
                   ['label' => 'Permission', 'icon' => 'fas fa-shield-alt', 'url' => ['/admin']],
               ]
           ],

           [
               'label' => 'Settings',
               'icon' => 'fas fa-cogs',
               'url' => '',
               'items' => [
                   [
                       'label' => 'Basic',
                       'icon' => 'fas fa-sliders-h',
                       'url' => '',
                       'items' => [
                           ['label' => 'Outlet', 'icon' => 'fas fa-store', 'url' => ['/outlet/index']],
                           ['label' => 'LC', 'icon' => 'fas fa-file-alt', 'url' => ['/lc/index']],
                           ['label' => 'Unit', 'icon' => 'fas fa-ruler-combined', 'url' => ['/product-unit/index']],
                           ['label' => 'City', 'icon' => 'fas fa-city', 'url' => ['/city/index']],
                           ['label' => 'Bank', 'icon' => 'fas fa-university', 'url' => ['/bank/index']],
                           ['label' => 'Branch', 'icon' => 'fas fa-code-branch', 'url' => ['/branch/index']],
                           ['label' => 'Supplier', 'icon' => 'fas fa-truck-loading', 'url' => ['/buyer/index']],
                           ['label' => 'Transport', 'icon' => 'fas fa-shipping-fast', 'url' => ['/transport/index']],
                           ['label' => 'Warehouse', 'icon' => 'fas fa-warehouse', 'url' => ['/warehouse/index']],
                       ]
                   ],
                   [
                       'label' => 'Product',
                       'icon' => 'fas fa-cube',
                       'url' => '#',
                       'items' => [
                           ['label' => 'Cart', 'icon' => 'fas fa-shopping-cart', 'url' => ['/sales-draft/index']],
                           ['label' => 'Item', 'icon' => 'fas fa-box', 'url' => ['/item/index']],
                           ['label' => 'Brand', 'icon' => 'fas fa-tags', 'url' => ['/brand/index']],
                           ['label' => 'Mapping', 'icon' => 'fas fa-project-diagram', 'url' => ['/brand-map/index']],
                           ['label' => 'Size', 'icon' => 'fas fa-expand-arrows-alt', 'url' => ['/size/index']],
                           ['label' => 'Price', 'icon' => 'fas fa-dollar-sign', 'url' => ['/product-items-price']],
                       ],
                   ],
                   [
                       'label' => 'Payment Type',
                       'icon' => 'fas fa-credit-card',
                       'url' => '',
                       'items' => [
                           ['label' => 'Payment Method', 'icon' => 'fas fa-credit-card', 'url' => ['/payment-type/index']],
                           ['label' => 'Expense', 'icon' => 'fas fa-money-check-alt', 'url' => ['/expense-type/index']],
                           ['label' => 'LC Payment', 'icon' => 'fas fa-file-invoice-dollar', 'url' => ['/lc-payment-type/index']],
                           ['label' => 'Reconciliation', 'icon' => 'fas fa-balance-scale-left', 'url' => ['/reconciliation-type/index']],
                       ]
                   ]
               ]
           ],

           [
               'label' => 'Configuration',
               'icon' => 'fas fa-wrench',
               'url' => '#',
               'items' => [
                   ['label' => 'App', 'icon' => 'fas fa-cog', 'url' => ['/app-settings/index']],
                   ['label' => 'SMS Gateway', 'icon' => 'fas fa-sms', 'url' => ['/sms-gateway/index']],
                   [
                       'label' => 'Queue',
                       'icon' => 'fas fa-tasks',
                       'url' => '#',
                       'items' => [
                           ['label' => 'Queue', 'icon' => 'fas fa-tasks', 'url' => ['/notification-queue/index']],
                           ['label' => 'Template', 'icon' => 'fas fa-envelope-open-text', 'url' => ['/template/index']],
                       ]
                   ]
               ],
           ],

           [
               'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
               'url' => ['/admin/user/logout'],
               'template' => '<a href="{url}" data-method="post"><i class="fas fa-sign-out-alt"></i> <span>{label}</span></a>',
               'visible' => !Yii::$app->user->isGuest,
           ],
       ];


        $menuItems = Helper::filter($menuItems);

        echo dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => $menuItems
            ]
        )

        ?>


        <?php
//        $callback = function ($menu) {
//            $data = @json_decode($menu['data'], true);
//            $icon = isset($data['icon']) ? $data['icon'] : 'fa-regular fa-circle'; // fallback icon
//            return [
//                'label' => $menu['name'],
//                'icon' => $icon,
//                'url'   => [$menu['route']],
//                'items' => $menu['children'],
//            ];
//        };
//
////        echo "<pre>";
//        $items = MenuHelper::getAssignedMenu(Yii::$app->user->id, null, $callback, true);
////        print_r($items);
////        die();
//
//        echo Menu::widget([
//            'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
//            'items' => MenuHelper::getAssignedMenu(Yii::$app->user->id, null, $callback, true),
//        ]);
        ?>

    </section>

</aside>
