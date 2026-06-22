<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'overview'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Welcome back, <?= htmlspecialchars(explode(' ', $user->name ?? $user['name'] ?? 'User')[0]) ?>!</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Here's what's happening with your account today.</p>
            </div>
            <a href="/shop" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-store"></i> Continue Shopping
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            <?php
            $statsCards = [
                ['label' => 'Total Orders', 'value' => $totalOrders ?? 0, 'icon' => 'fa-shopping-bag', 'bg' => 'bg-blue-600'],
                ['label' => 'Pending Orders', 'value' => $pendingOrders ?? 0, 'icon' => 'fa-clock', 'bg' => 'bg-yellow-600'],
                ['label' => 'Total Spent', 'value' => ($geo_currency_symbol ?? 'GH₵') . ' ' . number_format($totalSpent ?? 0, 2), 'icon' => 'fa-credit-card', 'bg' => 'bg-green-600'],
                ['label' => 'Wishlist Items', 'value' => $wishlistCount ?? 0, 'icon' => 'fa-heart', 'bg' => 'bg-pink-600'],
            ];
            ?>
            <?php foreach ($statsCards as $card): ?>
                <div class="relative overflow-hidden rounded-xl shadow-sm bg-white dark:bg-gray-800 p-5 group hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= $card['label'] ?></p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= $card['value'] ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-lg <?= $card['bg'] ?> flex items-center justify-center shadow-sm">
                            <i class="fas <?= $card['icon'] ?> text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
                        <a href="/orders" class="text-sm text-primary-700 dark:text-primary-400 hover:underline font-medium">View All</a>
                    </div>
                    <?php $recentOrders = $recentOrders ?? $orders ?? []; ?>
                    <?php if (empty($recentOrders)): ?>
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-box text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No orders yet</p>
                            <a href="/shop" class="mt-2 inline-block text-sm text-primary-700 dark:text-primary-400 hover:underline font-medium">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Order #</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Total</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                                        <?php
                                        $orderStatus = $order->order_status ?? $order['order_status'] ?? $order->status ?? $order['status'] ?? 'pending';
                                        $statusStyles = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                            'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        ];
                                        $label = ucfirst($orderStatus);
                                        ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($order->order_number ?? $order['order_number'] ?? $order->id ?? $order['id'] ?? '') ?></td>
                                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></td>
                                            <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$orderStatus] ?? $statusStyles['pending'] ?>"><?= $label ?></span></td>
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= ($geo_currency_symbol ?? 'GH₵') ?> <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></td>
                                            <td class="px-5 py-4"><a href="/orders/<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 text-xs font-medium rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/40 transition">View <i class="fas fa-arrow-right text-[10px]"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Links</h2>
                    <div class="space-y-2">
                        <?php
                        $links = [
                            ['url' => '/shop', 'icon' => 'fa-store', 'label' => 'Browse Shop', 'color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
                            ['url' => '/orders', 'icon' => 'fa-box', 'label' => 'My Orders', 'color' => 'text-green-600 dark:text-green-400', 'bg' => 'bg-green-50 dark:bg-green-900/20'],
                            ['url' => '/wishlist', 'icon' => 'fa-heart', 'label' => 'Wishlist', 'color' => 'text-pink-600 dark:text-pink-400', 'bg' => 'bg-pink-50 dark:bg-pink-900/20'],
                            ['url' => '/profile', 'icon' => 'fa-user-cog', 'label' => 'Profile Settings', 'color' => 'text-purple-600 dark:text-purple-400', 'bg' => 'bg-purple-50 dark:bg-purple-900/20'],
                            ['url' => '/addresses', 'icon' => 'fa-map-marker-alt', 'label' => 'Addresses', 'color' => 'text-orange-600 dark:text-orange-400', 'bg' => 'bg-orange-50 dark:bg-orange-900/20'],
                            ['url' => '/dashboard/notifications', 'icon' => 'fa-bell', 'label' => 'Notifications', 'color' => 'text-indigo-600 dark:text-indigo-400', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20'],
                        ];
                        ?>
                        <?php foreach ($links as $link): ?>
                            <a href="<?= $link['url'] ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">
                                <span class="w-9 h-9 rounded-lg <?= $link['bg'] ?> flex items-center justify-center <?= $link['color'] ?> group-hover:scale-105 transition">
                                    <i class="fas <?= $link['icon'] ?>"></i>
                                </span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= $link['label'] ?></span>
                                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 ml-auto text-xs"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
