<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'orders'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">All Orders</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total: <strong><?= number_format($totalOrders ?? $total ?? 0) ?></strong> orders</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-5">
            <?php
            $currentFilter = $_GET['status'] ?? 'all';
            $filters = [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'pending', 'label' => 'Pending'],
                ['key' => 'processing', 'label' => 'Processing'],
                ['key' => 'shipped', 'label' => 'Shipped'],
                ['key' => 'delivered', 'label' => 'Delivered'],
                ['key' => 'cancelled', 'label' => 'Cancelled'],
            ];
            ?>
            <?php foreach ($filters as $f): ?>
                <a href="?status=<?= $f['key'] ?>"
                   class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition <?= $currentFilter === $f['key'] ? 'bg-primary-700 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' ?>">
                    <?= $f['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php $orders = $orders ?? []; ?>
        <?php if (empty($orders)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No orders found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Orders will appear once customers start purchasing.</p>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Order #</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Vendor</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Items</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Total</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Payment</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $status = $order->status ?? $order['status'] ?? 'pending';
                                $paymentStatus = $order->payment_status ?? $order['payment_status'] ?? 'unpaid';
                                $statusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300', 'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                $paymentStyles = ['paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'unpaid' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'refunded' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300', 'partial' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'];
                                $items = $order->items ?? $order['items'] ?? [];
                                $itemCount = is_array($items) ? count($items) : 0;
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($order->customer_name ?? $order['customer_name'] ?? $order->user->name ?? $order['user']['name'] ?? 'Guest') ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($order->vendor_name ?? $order['vendor_name'] ?? $order->vendor->store_name ?? $order['vendor']['store_name'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= $itemCount ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $paymentStyles[$paymentStatus] ?? $paymentStyles['unpaid'] ?>"><?= ucfirst($paymentStatus) ?></span></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['pending'] ?>"><?= ucfirst($status) ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></td>
                                    <td class="px-5 py-4">
                                        <a href="/admin/orders/<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 text-xs font-medium rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/40 transition">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Page <?= $currentPage ?? 1 ?> of <?= $totalPages ?></p>
                    <div class="flex gap-2">
                        <?php if (($currentPage ?? 1) > 1): ?>
                            <a href="?page=<?= ($currentPage ?? 1) - 1 ?>&status=<?= $currentFilter ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&status=<?= $currentFilter ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($currentPage ?? 1) == $i ? 'bg-primary-700 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if (($currentPage ?? 1) < $totalPages): ?>
                            <a href="?page=<?= ($currentPage ?? 1) + 1 ?>&status=<?= $currentFilter ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>