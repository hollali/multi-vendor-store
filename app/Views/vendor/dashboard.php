<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'overview'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Good <?= (int)date('H') < 12 ? 'morning' : ((int)date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= htmlspecialchars(explode(' ', $user->name ?? $user['name'] ?? 'Vendor')[0]) ?>!</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/vendor/products/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-plus-circle"></i> New Product
                </a>
            </div>
        </div>

        <!-- Store Card -->
        <?php if ($store ?? false): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 lg:p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/30 dark:to-primary-800/20 flex items-center justify-center overflow-hidden flex-shrink-0 ring-2 ring-primary-100 dark:ring-primary-800/30">
                        <?php $logo = $store->logo ?? $store['logo'] ?? ''; ?>
                        <?php if ($logo): ?>
                            <img src="<?= htmlspecialchars($logo) ?>" alt="" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-store text-primary-500 text-xl"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white truncate"><?= htmlspecialchars($store->store_name ?? $store['store_name'] ?? $store->name ?? $store['name'] ?? 'My Store') ?></h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($store->email ?? $store['email'] ?? '') ?></p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-medium rounded-lg">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <?= ($store->is_verified ?? $store['is_verified'] ?? 0) ? 'Verified' : 'Active' ?>
                        </span>
                    </div>
                    <a href="/vendor/store-settings" class="flex-shrink-0 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            <?php
            $stats = [
                ['label' => 'Total Products', 'value' => number_format($totalProducts ?? 0), 'icon' => 'fa-box', 'color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'ring' => 'ring-blue-100 dark:ring-blue-800/30'],
                ['label' => 'Active Listings', 'value' => number_format($activeProducts ?? 0), 'icon' => 'fa-check-circle', 'color' => 'text-emerald-600 dark:text-emerald-400', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'ring' => 'ring-emerald-100 dark:ring-emerald-800/30'],
                ['label' => 'Total Orders', 'value' => number_format($totalOrders ?? 0), 'icon' => 'fa-shopping-bag', 'color' => 'text-violet-600 dark:text-violet-400', 'bg' => 'bg-violet-50 dark:bg-violet-900/20', 'ring' => 'ring-violet-100 dark:ring-violet-800/30'],
                ['label' => 'Revenue', 'value' => ($geo_currency_symbol ?? 'GH₵') . number_format($totalRevenue ?? 0, 2), 'icon' => 'fa-credit-card', 'color' => 'text-amber-600 dark:text-amber-400', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'ring' => 'ring-amber-100 dark:ring-amber-800/30'],
            ];
            ?>
            <?php foreach ($stats as $s): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 lg:p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"><?= $s['label'] ?></span>
                        <span class="w-9 h-9 rounded-lg <?= $s['bg'] ?> flex items-center justify-center ring-1 <?= $s['ring'] ?>">
                            <i class="fas <?= $s['icon'] ?> <?= $s['color'] ?> text-sm"></i>
                        </span>
                    </div>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white"><?= $s['value'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Orders -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
                            <?php if ($pendingOrders > 0): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                                    <?= $pendingOrders ?> pending
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="/vendor/orders" class="text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">
                            View All <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                    <?php $recentOrders = $recentOrders ?? $orders ?? []; ?>
                    <?php if (empty($recentOrders)): ?>
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center">
                                <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-3xl"></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">No orders yet</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">When customers start buying your products, their orders will show up here.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                                        <th class="px-5 lg:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order</th>
                                        <th class="px-5 lg:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                        <th class="px-5 lg:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                        <th class="px-5 lg:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment</th>
                                        <th class="px-5 lg:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <?php
                                        $orderStatus = $order->order_status ?? $order['order_status'] ?? $order->status ?? $order['status'] ?? 'pending';
                                        $payStatus = $order->payment_status ?? $order['payment_status'] ?? '';
                                        $statusStyles = [
                                            'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-800/30',
                                            'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 ring-1 ring-blue-200 dark:ring-blue-800/30',
                                            'shipped' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400 ring-1 ring-violet-200 dark:ring-violet-800/30',
                                            'delivered' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800/30',
                                            'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-800/30',
                                            'refunded' => 'bg-gray-50 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400 ring-1 ring-gray-200 dark:ring-gray-700',
                                        ];
                                        $payStyles = [
                                            'paid' => 'text-emerald-600 dark:text-emerald-400',
                                            'unpaid' => 'text-amber-600 dark:text-amber-400',
                                            'pending' => 'text-gray-500 dark:text-gray-400',
                                            'failed' => 'text-red-600 dark:text-red-400',
                                        ];
                                        $label = ucfirst($orderStatus);
                                        ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition cursor-pointer" onclick="window.location='/vendor/orders/<?= $order->id ?? $order['id'] ?? 0 ?>'">
                                            <td class="px-5 lg:px-6 py-4">
                                                <span class="font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($order->order_number ?? $order['order_number'] ?? $order->id ?? $order['id'] ?? '') ?></span>
                                            </td>
                                            <td class="px-5 lg:px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $statusStyles[$orderStatus] ?? $statusStyles['pending'] ?>">
                                                    <?= $label ?>
                                                </span>
                                            </td>
                                            <td class="px-5 lg:px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                                <?= ($geo_currency_symbol ?? 'GH₵') ?><?= number_format($order->total ?? $order['total'] ?? 0, 2) ?>
                                            </td>
                                            <td class="px-5 lg:px-6 py-4">
                                                <span class="inline-flex items-center gap-1 text-xs font-medium <?= $payStyles[$payStatus] ?? 'text-gray-500' ?>">
                                                    <span class="w-1.5 h-1.5 rounded-full <?= $payStatus === 'paid' ? 'bg-emerald-500' : ($payStatus === 'unpaid' ? 'bg-amber-500' : 'bg-gray-400') ?>"></span>
                                                    <?= ucfirst($payStatus ?: 'unknown') ?>
                                                </span>
                                            </td>
                                            <td class="px-5 lg:px-6 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                <?= date('M d, Y', strtotime($order->created_at ?? $order['created_at'] ?? '')) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        $actions = [
                            ['url' => '/vendor/products/create', 'icon' => 'fa-plus-circle', 'label' => 'Add Product', 'desc' => 'List a new item', 'color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
                            ['url' => '/vendor/orders', 'icon' => 'fa-clipboard-list', 'label' => 'Manage Orders', 'desc' => 'Process & fulfill', 'color' => 'text-emerald-600 dark:text-emerald-400', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20'],
                            ['url' => '/vendor/store-settings', 'icon' => 'fa-store', 'label' => 'Store Settings', 'desc' => 'Update your store', 'color' => 'text-violet-600 dark:text-violet-400', 'bg' => 'bg-violet-50 dark:bg-violet-900/20'],
                            ['url' => '/vendor/earnings', 'icon' => 'fa-chart-line', 'label' => 'Earnings', 'desc' => 'View payouts', 'color' => 'text-amber-600 dark:text-amber-400', 'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
                            ['url' => '/vendor/coupons', 'icon' => 'fa-tag', 'label' => 'Coupons', 'desc' => 'Create promotions', 'color' => 'text-pink-600 dark:text-pink-400', 'bg' => 'bg-pink-50 dark:bg-pink-900/20'],
                            ['url' => '/vendor/shipping', 'icon' => 'fa-truck', 'label' => 'Shipping Rates', 'desc' => 'Configure zones', 'color' => 'text-indigo-600 dark:text-indigo-400', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20'],
                        ];
                        ?>
                        <div class="space-y-0.5">
                            <?php foreach ($actions as $action): ?>
                                <a href="<?= $action['url'] ?>" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">
                                    <span class="w-10 h-10 rounded-lg <?= $action['bg'] ?> flex items-center justify-center <?= $action['color'] ?> group-hover:scale-110 transition">
                                        <i class="fas <?= $action['icon'] ?>"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $action['label'] ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= $action['desc'] ?></p>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Products Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Products Overview</h2>
                    <?php
                    $total = max(1, $totalProducts ?? 1);
                    $activePct = round(($activeProducts ?? 0) / $total * 100);
                    $inactivePct = 100 - $activePct;
                    ?>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1.5">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Active</span>
                                <span class="text-gray-500 dark:text-gray-400"><?= number_format($activeProducts ?? 0) ?> / <?= number_format($totalProducts ?? 0) ?></span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: <?= $activePct ?>%"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3 text-center">
                                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400"><?= $activePct ?>%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Active Rate</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3 text-center">
                                <p class="text-lg font-bold text-gray-900 dark:text-white"><?= $pendingOrders ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>