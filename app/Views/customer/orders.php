<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'orders'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">My Orders</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= $totalOrders ?? count($orders ?? []) ?> order(s) found</p>
            </div>
        </div>

        <?php
        $currentFilter = $_GET['status'] ?? 'all';
        $filters = ['all' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
        ?>
        <div class="flex flex-wrap gap-2 mb-6">
            <?php foreach ($filters as $key => $label): ?>
                <a href="?status=<?= $key ?>" class="px-4 py-2 text-sm font-medium rounded-full transition <?= $currentFilter === $key ? 'bg-primary-700 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <?php $orders = $orders ?? []; ?>
            <?php if (empty($orders)): ?>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No orders yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Looks like you haven't placed any orders yet.</p>
                    <a href="/shop" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-store"></i> Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Order #</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Items</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Total</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Tracking</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $status = $order->status ?? $order['status'] ?? 'pending';
                                $statusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300', 'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                $label = ucfirst($status);
                                $tracking = $order->tracking_number ?? $order['tracking_number'] ?? '';
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300"><?= $order->items_count ?? $order['items_count'] ?? 0 ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GHS <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['pending'] ?>"><?= $label ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 text-xs"><?= $tracking ? htmlspecialchars($tracking) : '<span class="text-gray-400">—</span>' ?></td>
                                    <td class="px-5 py-4"><a href="/orders/<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 text-xs font-medium rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/40 transition">View <i class="fas fa-arrow-right text-[10px]"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages ?? 1 > 1): ?>
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Page <?= $currentPage ?? 1 ?> of <?= $totalPages ?? 1 ?></p>
                        <div class="flex gap-2">
                            <?php if (($currentPage ?? 1) > 1): ?>
                                <a href="?page=<?= ($currentPage ?? 1) - 1 ?>&status=<?= $currentFilter ?>" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition"><i class="fas fa-chevron-left"></i> Prev</a>
                            <?php endif; ?>
                            <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                                <a href="?page=<?= ($currentPage ?? 1) + 1 ?>&status=<?= $currentFilter ?>" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Next <i class="fas fa-chevron-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
