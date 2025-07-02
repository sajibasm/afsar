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
                <p><?= isset(Yii::$app->user->identity) ? ucwords(Yii::$app->user->identity->first_name. ' '.Yii::$app->user->identity->last_name) : ''; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>


        <?php


       $menuItems = [
           ['label' => 'Options', 'options' => ['class' => 'header']],

           [
               'label' => 'Profile',
               'icon' => 'fas fa-user-circle',
               'items' => [
                   [
                       'label' => 'View Profile',
                       'icon' => 'fas fa-id-badge',
                       'url' => ['/admin/user/profile'],
                   ],
                   [
                       'label' => 'Activity Log',
                       'icon' => 'fas fa-history',
                       'url' => ['/admin/user/login-log'], // adjust route as needed
                   ],
               ],
           ],

           ['label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => ['/site/index']],

           [
               'label' => 'Sales',
               'icon' => 'fas fa-cash-register',
               'url' => '#',
               'items' => [
                   ['label' => 'New Sale', 'icon' => 'fas fa-cash-register', 'url' => ['/sales/store']],
                   ['label' => 'Sales Records', 'icon' => 'fas fa-receipt', 'url' => ['/sales/index']],
                   ['label' => 'Cart Holds', 'icon' => 'fas fa-shopping-cart', 'url' => ['/sales-draft/index']],
               ]
           ],

           [
               'label' => 'Return',
               'icon' => 'fas fa-tools',
               'url' => '#',
               'items' => [
                   [
                       'label' => 'Create Return',
                       'icon' => 'fas fa-box-open',
                       'url' => ['/sales-return/create']
                   ],
                   [
                       'label' => 'Return Records',
                       'icon' => 'fas fa-undo-alt',
                       'url' => ['/sales-return/index']
                   ],
               ]
           ],

           [
               'label' => 'Retailer Ledger',
               'icon' => 'fas fa-book',
               'url' => '#',
               'items' => [
                   ['label' => 'Record Supply', 'icon' => 'fas fa-plus-square', 'url' => ['//market-book/create']],
                   ['label' => 'Generate Invoice', 'icon' => 'fas fa-file-invoice', 'url' => ['/market/generate-invoice']],
                   ['label' => 'Supply Records', 'icon' => 'fas fa-book-open', 'url' => ['//market-book/index']],
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
                           ['label' => 'Stock Records', 'icon' => 'fas fa-history', 'url' => ['/product-stock/index']],
                           ['label' => 'Items Records', 'icon' => 'fas fa-list-alt', 'url' => ['/product-stock/items']],
                       ]
                   ],
                   [
                       'label' => 'Store',
                       'icon' => 'fas fa-store',
                       'url' => '',
                       'items' => [
                           ['label' => 'Transfer', 'icon' => 'fas fa-exchange-alt', 'url' => ['/product-stock-movement/store']],
                           ['label' => 'Stock Records', 'icon' => 'fas fa-history', 'url' => ['/product-stock-outlet/index']],
                           ['label' => 'Stock Transactions', 'icon' => 'fas fa-file-alt', 'url' => ['/product-statement-outlet/index']]
                       ]
                   ]
               ]
           ],

           [
               'label' => 'Expense',
               'icon' => 'fas fa-shopping-bag',
               'url' => '#',
               'items' => [

                   [
                       'label' => 'LC',
                       'icon' => 'fas fa-file-contract',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Payment', 'icon' => 'fas fa-plus-circle', 'url' => ['/lc-payment/create']],
                           ['label' => 'LC Records', 'icon' => 'fas fa-file-contract', 'url' => ['/lc-payment/index']],
                       ]
                   ],

                   [
                       'label' => 'Expense',
                       'icon' => 'fas fa-money-check-alt',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Payment', 'icon' => 'fas fa-plus-circle', 'url' => ['/expense/create']],
                           ['label' => 'Expense Records', 'icon' => 'fas fa-money-check-alt', 'url' => ['/expense/index']],
                       ]
                   ],

                   [
                       'label' => 'Warehouse',
                       'icon' => 'fas fa-warehouse',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Payment', 'icon' => 'fas fa-plus-circle', 'url' => ['/warehouse-payment/create']],
                           ['label' => 'Warehouse Records', 'icon' => 'fas fa-warehouse', 'url' => ['/warehouse-payment/index']],
                       ]
                   ],

                   [
                       'label' => 'Reconciliation',
                       'icon' => 'fas fa-balance-scale',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Reconciliation', 'icon' => 'fas fa-plus-circle', 'url' => ['/bank-reconciliation/create']],
                           ['label' => 'Reconciliation Records', 'icon' => 'fas fa-balance-scale', 'url' => ['/bank-reconciliation/index']],
                       ]
                   ],

               ],
           ],

           [
               'label' => 'Accounts',
               'icon' => 'fas fa-folder-open',
               'url' => '#',
               'items' => [
                   [
                       'label' => 'Withdraw',
                       'icon' => 'fas fa-arrow-circle-down',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Withdraw', 'icon' => 'fas fa-plus-circle', 'url' => ['/withdraw/create']],
                           ['label' => 'Withdraw Records', 'icon' => 'fas fa-arrow-circle-down', 'url' => ['/withdraw/index']],
                       ]
                   ],

                   [
                       'label' => 'Hand Received',
                       'icon' => 'fas fa-handshake',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Hand Received', 'icon' => 'fas fa-plus-circle', 'url' => ['/cash-hand-received/create']],
                           ['label' => 'Hand Received Records', 'icon' => 'fas fa-handshake', 'url' => ['/cash-hand-received/index']],
                       ]
                   ],
               ],
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
                       'icon' => 'fas fa-folder-open',
                       'url' => '',
                       'items' => [
                           ['label' => 'Withdraw', 'icon' => 'fas fa-arrow-circle-down', 'url' => ['/reports/withdraw']],
                           ['label' => 'Hand Received', 'icon' => 'fas fa-handshake', 'url' => ['/reports/cash-hand-received']],
                       ]
                   ],
                   [
                       'label' => 'Customer',
                       'icon' => 'fas fa-user-friends',
                       'url' => '#',
                       'items' => [
                           ['label' => 'Payment Details', 'icon' => 'fas fa-hand-holding-usd', 'url' => ['/client-payment-details/index']],
                           ['label' => 'Refund', 'icon' => 'fas fa-receipt', 'url' => ['/customer-withdraw/index']],
                           ['label' => 'Dues', 'icon' => 'fas fa-file-invoice-dollar', 'url' => ['/customer-account/dues']],
                           ['label' => 'Invoice', 'icon' => 'fas fa-file-invoice', 'url' => ['/customer-account/index']],
                       ],
                   ],
                   [
                       'label' => 'Payment',
                       'icon' => 'fas fa-credit-card',
                       'url' => '',
                       'items' => [
                           ['label' => 'Received', 'icon' => 'fas fa-money-check-alt', 'url' => ['/reports/customer-payment-received']],
                           ['label' => 'Refund', 'icon' => 'fas fa-receipt', 'url' => ['/reports/customer-payment-refund']],
                       ]
                   ],
                   [
                       'label' => 'Expense',
                       'icon' => 'fas fa-shopping-bag',
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
               'label' => 'Customer',
               'icon' => 'fas fa-user-friends',
               'url' => '#',
               'items' => [
                   [
                       'label' => 'Customer',
                       'icon' => 'fas fa-user',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Customer', 'icon' => 'fas fa-plus-circle', 'url' => ['/client/create']],
                           ['label' => 'Customer Records', 'icon' => 'fas fa-user', 'url' => ['/client/index']],
                       ]
                   ],

                   [
                       'label' => 'Payment',
                       'icon' => 'fas fa-history',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Payment', 'icon' => 'fas fa-plus-circle', 'url' => ['/client-payment-history/create']],
                           ['label' => 'Payment Records', 'icon' => 'fas fa-history', 'url' => ['/client-payment-history/index']],
                       ]
                   ],
               ],
           ],

           [
               'label' => 'Payroll',
               'icon' => 'fas fa-credit-card',
               'url' => '',
               'items' => [
                       ['label' => 'Payslip', 'icon' => 'fas fa-file-alt', 'url' => ['/salary-history/payroll-slip']],
                       ['label' => 'Salary', 'icon' => 'fas fa-money-bill-wave', 'url' => ['/salary-history/salary']],
                       [
                           'label' => 'Upfront',
                           'icon' => 'fas fa-hand-holding-usd',
                           'url' => ['#'],
                           'items' => [
                               ['label' => 'Crate Upfront', 'icon' => 'fas fa-plus-circle', 'url' => ['/salary-history/create']],
                               ['label' => 'Upfront Records', 'icon' => 'fas fa-hand-holding-usd', 'url' => ['/salary-history/advance-salary']],
                           ]
                       ],
               ]
           ],

           [
                   'label' => 'Employee',
                    'icon' => 'fas fa-user-circle',
                       'url' => '',
                       'items' => [
                           [
                               'label' => 'Employee',
                               'icon' => 'fas fa-user',
                               'url' => ['#'],
                               'items' => [
                                   ['label' => 'Create Employee', 'icon' => 'fas fa-plus-circle', 'url' => ['/employee/create']],
                                   ['label' => 'Employee Records', 'icon' => 'fas fa-user', 'url' => ['/employee/index']],
                               ]
                           ],
                           [
                               'label' => 'Position',
                               'icon' => 'fas fa-user-tag',
                               'url' => ['#'],
                               'items' => [
                                   ['label' => 'Create Position', 'icon' => 'fas fa-plus-circle', 'url' => ['/employee-designation/create']],
                                   ['label' => 'Position Records', 'icon' => 'fas fa-user-tag', 'url' => ['/employee-designation/index']],
                               ]
                           ],
                       ]
                   ],

                    [
                        'label' => 'User Access',
                        'icon' => 'fas fa-universal-access', // General access icon, suitable for parent
                        'url' => '',
                        'items' => [
                            [
                                'label' => 'Create User',
                                'icon' => 'fas fa-user-plus', // Better icon for user creation
                                'url' => ['/user/create']
                            ],
                            [
                                'label' => 'User Records',
                                'icon' => 'fas fa-users', // Represents a group of users (records/list)
                                'url' => ['/user/index']
                            ],
                            [
                                'label' => 'Permission',
                                'icon' => 'fas fa-user-shield', // Represents permission/control
                                'url' => ['/admin']
                            ],
                        ]
                    ],

           [
               'label' => 'Basic Settings',
               'icon' => 'fas fa-sliders-h',
               'url' => '',
               'items' => [
                   [
                       'label' => 'Store',
                       'icon' => 'fas fa-store',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Store', 'icon' => 'fas fa-plus-circle', 'url' => ['/outlet/create']],
                           ['label' => 'Store Records', 'icon' => 'fas fa-store', 'url' => ['/outlet/index']],
                       ]
                   ],
                   [
                       'label' => 'LC',
                       'icon' => 'fas fa-file-alt',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create LC', 'icon' => 'fas fa-plus-circle', 'url' => ['/lc/create']],
                           ['label' => 'LC Records', 'icon' => 'fas fa-file-alt', 'url' => ['/lc/index']],
                       ]
                   ],

                   [
                       'label' => 'City',
                       'icon' => 'fas fa-city',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create City', 'icon' => 'fas fa-plus-circle', 'url' => ['/city/create']],
                           ['label' => 'City Records', 'icon' => 'fas fa-city', 'url' => ['/city/index']],
                       ]
                   ],

                   [
                       'label' => 'Bank',
                       'icon' => 'fas fa-university',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Bank', 'icon' => 'fas fa-plus-circle', 'url' => ['/bank/create']],
                           ['label' => 'Bank Records', 'icon' => 'fas fa-university', 'url' => ['/bank/index']],
                       ]
                   ],

                   [
                       'label' => 'Branch',
                       'icon' => 'fas fa-code-branch',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Bank', 'icon' => 'fas fa-plus-circle', 'url' => ['/branch/create']],
                           ['label' => 'Branch Records', 'icon' => 'fas fa-code-branch', 'url' => ['/branch/index']],
                       ]
                   ],

                   [
                       'label' => 'Supplier',
                       'icon' => 'fas fa-truck-loading',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Supplier', 'icon' => 'fas fa-plus-circle', 'url' => ['/buyer/create']],
                           ['label' => 'Supplier Records', 'icon' => 'fas fa-truck-loading', 'url' => ['/buyer/index']],
                       ]
                   ],

                   [
                       'label' => 'Transport',
                       'icon' => 'fas fa-shipping-fast',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Transport', 'icon' => 'fas fa-plus-circle', 'url' => ['/transport/create']],
                           ['label' => 'Transport Records', 'icon' => 'fas fa-shipping-fast', 'url' => ['/transport/index']],
                       ]
                   ],

                   [
                       'label' => 'Warehouse',
                       'icon' => 'fas fa-warehouse',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Warehouse', 'icon' => 'fas fa-plus-circle', 'url' => ['/warehouse/create']],
                           ['label' => 'Warehouse Records', 'icon' => 'fas fa-warehouse', 'url' => ['/warehouse/index']],
                       ]
                   ],

               ]
           ],

           [
               'label' => 'Product Settings',
               'icon' => 'fas fa-cube',
               'url' => '#',
               'items' => [
                   ['label' => 'Price', 'icon' => 'fas fa-dollar-sign', 'url' => ['/product-items-price']],
                   [
                       'label' => 'Unit',
                       'icon' => 'fas fa-ruler-combined',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Unit', 'icon' => 'fas fa-plus-circle', 'url' => ['/product-unit/create']],
                           ['label' => 'Unit Records', 'icon' => 'fas fa-ruler-combined', 'url' => ['/product-unit/index']],
                       ]
                   ],
                   [
                       'label' => 'Item',
                       'icon' => 'fas fa-box',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Item', 'icon' => 'fas fa-plus-circle', 'url' => ['/item/create']],
                           ['label' => 'Item Records', 'icon' => 'fas fa-box', 'url' => ['/item/index']],
                       ]
                   ],
                   [
                       'label' => 'Brand',
                       'icon' => 'fas fa-tags',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Brand', 'icon' => 'fas fa-plus-circle', 'url' => ['/brand/create']],
                           ['label' => 'Brand Records', 'icon' => 'fas fa-tags', 'url' => ['/brand/index']],
                       ]
                   ],
                   [
                       'label' => 'Mapping',
                       'icon' => 'fas fa-project-diagram',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Mapping', 'icon' => 'fas fa-plus-circle', 'url' => ['/brand-map/create']],
                           ['label' => 'Mapped Records', 'icon' => 'fas fa-project-diagram', 'url' => ['/brand-map/index']],
                       ]
                   ],
                   [
                       'label' => 'Size',
                       'icon' => 'fas fa-expand-arrows-alt',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Size', 'icon' => 'fas fa-plus-circle', 'url' => ['/size/create']],
                           ['label' => 'Size Records', 'icon' => 'fas fa-expand-arrows-alt', 'url' => ['/size/index']],
                       ]
                   ],
               ],
           ],

           [
               'label' => 'Payment Settings',
               'icon' => 'fas fa-credit-card',
               'url' => '',
               'items' => [
                   [
                       'label' => 'Payment',
                       'icon' => 'fas fa-credit-card',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Payment Type', 'icon' => 'fas fa-plus-circle', 'url' => ['/payment-type/create']],
                           ['label' => 'Payment Records', 'icon' => 'fas fa-credit-card', 'url' => ['/payment-type/index']],
                       ]
                   ],
                   [
                       'label' => 'Expense',
                       'icon' => 'fas fa-money-check-alt',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Expense Type', 'icon' => 'fas fa-plus-circle', 'url' => ['/expense-type/create']],
                           ['label' => 'Expense Records', 'icon' => 'fas fa-money-check-alt', 'url' => ['/expense-type/index']],
                       ]
                   ],
                   [
                       'label' => 'LC',
                       'icon' => 'fas fa-file-invoice-dollar',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create LC Type', 'icon' => 'fas fa-plus-circle', 'url' => ['/lc-payment-type/create']],
                           ['label' => 'LC Records', 'icon' => 'fas fa-file-invoice-dollar', 'url' => ['/lc-payment-type/index']],
                       ]
                   ],
                   [
                       'label' => 'Reconciliation',
                       'icon' => 'fas fa-balance-scale-left',
                       'url' => ['#'],
                       'items' => [
                           ['label' => 'Create Reconciliation Type', 'icon' => 'fas fa-plus-circle', 'url' => ['/reconciliation-type/create']],
                           ['label' => 'Reconciliation Records', 'icon' => 'fas fa-balance-scale-left', 'url' => ['/reconciliation-type/index']],
                       ]
                   ],
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


//        /$menuItems = Helper::filter($menuItems);

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
