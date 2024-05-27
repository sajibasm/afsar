<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= Yii::getAlias('@web') . '/uploads/employee/user.png' ?>" class="img-circle"
                     alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= isset(Yii::$app->user->identity) ? ucwords(Yii::$app->user->identity->username) : ''; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [
                    ['label' => 'Options', 'options' => ['class' => 'header']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    ['label' => 'Sell', 'icon' => 'fas fa-cart-plus', 'url' => ['/sales/outlet'],],
                    ['label' => 'MarketBook', 'icon' => 'far fa-bookmark', 'url' => ['/sales/create'],],

                    [
                        'label' => 'Sales',
                        'icon' => 'fas fa-cart-plus',
                        'url' => '#',
                        'items' => [
                            //['label' => 'Sell', 'url' => ['/sales/create'],],
                            ['label' => 'Sales', 'url' => ['/sales/index'],],

                            //['label' => 'Market Book',  'url' => ['/market-book/create'],],
                            ['label' => 'Marketbook', 'url' => ['/market-book/index'],],

                            //['label' => 'Return', 'url' => ['/sales-return/verify'],],
                            //['label' => 'Service/Repair', 'url' => ['/sales-return/verify-repair']],
                            ['label' => 'Return', 'url' => ['/sales-return/index'],],
                        ],
                    ],

                    [
                        'label' => 'Stock',
                        'icon' => 'fab fa-stack-exchange',
                        'url' => '',
                        'items' => [
                            [
                                'label' => 'Inventory',
                                'icon' => 'fab fa-stack-exchange',
                                'url' => '',
                                'items' => [
                                    ['label' => 'New', 'url' => ['/product-stock/create']],
                                    ['label' => 'Transfer', 'url' => ['/product-stock/transfer']],
                                    ['label' => 'Stock History', 'url' => ['/product-stock/index']],
                                    ['label' => 'Items History', 'url' => ['/product-stock/items']],

                                ]
                            ],

                            [
                                'label' => 'Outlet',
                                'icon' => 'fab fa-stack-exchange',
                                'url' => '',
                                'items' => [
                                    ['label' => 'Transfer', 'url' => ['/product-stock-movement/outlet']],
                                    ['label' => 'Stock History', 'url' => ['/product-stock-outlet/index']],
                                    ['label' => 'Stock Statement', 'url' => ['/product-statement-outlet/index']]
                                ]
                            ]
                        ]
                    ],

                    [
                        'label' => 'Accounts',
                        'icon' => 'fas fa-home',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Withdraw', 'url' => ['/withdraw/index']],
                            ['label' => 'Hand Received', 'url' => ['/cash-hand-received/index'],],
                        ],
                    ],

                    [
                        'label' => 'Expense',
                        'icon' => 'fas fa-money',
                        'url' => '#',
                        'items' => [
                            ['label' => 'LC', 'url' => ['/lc-payment/index']],
                            ['label' => 'Expense', 'url' => ['/expense/index']],
                            ['label' => 'Warehouse', 'url' => ['/warehouse-payment/index']],
                            ['label' => 'Reconciliation', 'url' => ['/bank-reconciliation/index']],
                        ],
                    ],

                    [
                        'label' => 'Reports',
                        'icon' => 'fas fa-bar-chart',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Sales',
                                //'icon' => 'fab fa-stack-exchange',
                                'url' => '',
                                'items' => [
                                    ['label' => 'Sales', 'url' => ['/reports/sales']],
                                    ['label' => 'Return', 'url' => ['/reports/return']],
                                    ['label' => 'Marketbook', 'url' => ['/reports/market']],
                                ]
                            ],

                            [
                                'label' => 'Stock',
                                //'icon' => 'fab fa-stack-exchange',
                                'url' => '',
                                'items' => [
                                    ['label' => 'History', 'url' => ['/reports/product-stock']],
                                    ['label' => 'Movement', 'url' => ['/reports/product-stock-movement']],
                                    ['label' => 'Low', 'url' => ['/reports/product-stock']],
                                ]
                            ],
                            [
                                'label' => 'Cash',
                                'url' => '',
                                'items' => [
                                    ['label' => 'History', 'url' => ['/reports/cash-book']],
                                    ['label' => 'Summery', 'url' => ['/reports/cash-book-summery']]
                                ]
                            ],
                            [
                                'label' => 'Bank',
                                'url' => '',
                                'items' => [
                                    ['label' => 'History', 'url' => ['/reports/deposit-book']],
                                    ['label' => 'Summery', 'url' => ['/reports/bank-book-summery']]
                                ]
                            ],

                            [
                                'label' => 'Accounts',
                                //'icon' => 'fab fa-stack-exchange',
                                'url' => '',
                                'items' => [
                                    ['label' => 'Withdraw', 'url' => ['/reports/withdraw']],
                                    ['label' => 'Hand Received','url' => ['/reports/cash-hand-received']],
                                ]
                            ],

                            [
                                'label' => 'Payment',
                                'url' => '',
                                'items' => [
                                    ['label' => 'Received', 'url' => ['/reports/customer-payment-received']],
                                    ['label' => 'Refund',  'url' => ['/reports/customer-payment-refund']],
                                ]
                            ],

                            [
                                'label' => 'Expense',
                                'url' => '',
                                'items' => [
                                    ['label' => 'Expense', 'url' => ['/reports/expense']],
                                    ['label' => 'Warehouse', 'url' => ['/reports/warehouse']], // client wise due Transaction
                                    ['label' => 'LC', 'url' => ['/reports/lc']], // client wise due Transaction
                                ]
                            ],

                            [
                                'label' => 'Product',
                                'url' => '',
                                'items' => [
                                    ['label' => 'Product', 'url' => ['/reports/product']],
                                    ['label' => 'Brand', 'url' => ['/reports/product-brand']],
                                    ['label' => 'Customer',  'url' => ['/reports/product-customer']],
                                ]
                            ],
                        ],
                    ],

                    [
                        'label' => 'Customer',
                        'icon' => 'fas fa-male',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Customer', 'url' => ['/client/index']],
                            ['label' => 'Refund', 'url' => ['/customer-withdraw/index']],
                            ['label' => 'Payment', 'url' => ['/client-payment-history/create']],
                            ['label' => 'Received', 'url' => ['/client-payment-history/index']],
                            ['label' => 'Payment Details', 'url' => ['/client-payment-details/index']],
                            ['label' => 'Dues', 'url' => ['/customer-account/dues']],
                            ['label' => 'Invoice', 'url' => ['/customer-account/index']],
                        ],
                    ],

                    [
                        'label' => 'Employee',
                        'icon' => 'fas fa-user-circle',
                        'url' => '#',
                        'items' => [

                            ['label' => 'Salary', 'url' => ['/salary-history/salary']], // client wise due Transaction
                            ['label' => 'Advance Salary', 'url' => ['/salary-history/advance-salary']],
                            //['label' => 'Payroll', 'url'=> ['/salary-history/index']],
                            ['label' => 'Remuneration', 'url' => ['/salary-history/create']],
                            ['label' => 'Monthly Payroll Slip', 'url' => ['/salary-history/payroll-slip']],

                            ['label' => 'Role', 'url' => ['/employee-designation/index']],
                            ['label' => 'Employee', 'url' => ['/employee/index']],


                        ],
                    ],

                    [
                        'label' => 'Product',
                        'icon' => 'fas fa-cube',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Cart', 'url' => ['/sales-draft/index']],
                            ['label' => 'Item', 'url' => ['/item/index']],
                            ['label' => 'Brand', 'url' => ['/brand/index']],
                            ['label' => 'Mapping', 'url' => ['/brand-map/index']],
                            ['label' => 'Size', 'url' => ['/size/index']],
                            ['label' => 'Price', 'url' => ['/product-items-price']],
                        ],
                    ],

                    [
                        'label' => 'Type',
                        'icon' => 'fas fa-cubes',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Payment', 'url' => ['/payment-type/index']],
                            ['label' => 'Expense', 'url' => ['/expense-type/index']],
                            ['label' => 'LC Payment', 'url' => ['/lc-payment-type/index']],
                            ['label' => 'Reconciliation', 'url' => ['/reconciliation-type/index']],
                            ['label' => 'Challan Condition', 'url' => ['/challan-condition/index']],

                        ],
                    ],

                    [
                        'label' => 'Common',
                        'icon' => 'fab fa-contao',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Outlet', 'url' => ['/outlet/index']],
                            ['label' => 'LC', 'url' => ['/lc/index']],
                            ['label' => 'Unit', 'url' => ['/product-unit/index']],
                            ['label' => 'City', 'url' => ['/city/index']],
                            ['label' => 'Bank', 'url' => ['/bank/index']],
                            ['label' => 'Branch', 'url' => ['/branch/index']],
                            ['label' => 'Supplier', 'url' => ['/buyer/index']],
                            ['label' => 'Transport', 'url' => ['/transport/index']],
                            ['label' => 'Warehouse', 'url' => ['/warehouse/index']],
                        ],
                    ],

                    [
                        'label' => 'Settings',
                        'icon' => 'fas fa-wrench',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Database', 'url' => ['/backup/index']],
                            ['label' => 'App', 'url' => ['/app-settings/index']],
                            ['label' => 'SMS Gateway', 'url' => ['/sms-gateway/index']],
                            [
                                'label' => 'Queue',
                                'icon' => 'fas fa-wrench',
                                'url' => ['#'],
                                'items'=>[
                                    ['label' => 'Queue', 'url' => ['/notification-queue/index']],
                                    ['label' => 'Template', 'url' => ['/template/index']],
                                ]
                            ]
                        ],
                    ],

                    ['label' => 'User', 'icon' => 'fas fa-cart-plus', 'url' => ['/user/index'],],
                    ['label' => 'Permission', 'icon' => 'fas fa-cart-plus', 'url' => ['/admin'],],

                ],
            ]
        ) ?>

    </section>

</aside>
