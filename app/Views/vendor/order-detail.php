<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'orders'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <a href="/vendor/orders" class="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-4 transition">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>

        <?php $order = $order ?? null; ?>
        <?php if (!$order): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Order not found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">The order you're looking for doesn't exist.</p>
            </div>
        <?php else: ?>
            <?php
            $status = $order->status ?? $order['status'] ?? 'pending';
            $statusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300', 'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
            $customerName = $order->customer_name ?? $order['customer_name'] ?? $order->user->name ?? $order['user']['name'] ?? 'Guest';
            $customerEmail = $order->customer_email ?? $order['customer_email'] ?? $order->user->email ?? $order['user']['email'] ?? '';
            $customerPhone = $order->customer_phone ?? $order['customer_phone'] ?? $order->user->phone ?? $order['user']['phone'] ?? '';
            ?>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order #<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?></h1>
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <span><i class="far fa-calendar-alt mr-1"></i> <?= htmlspecialchars(date('M d, Y \a\t h:i A', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></span>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <span><i class="far fa-user mr-1"></i> <?= htmlspecialchars($customerName) ?></span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['pending'] ?>"><?= ucfirst($status) ?></span>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">GH₵ <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Product</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">SKU</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Qty</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Price</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php $items = $order->items ?? $order['items'] ?? []; ?>
                                    <?php if (empty($items)): ?>
                                        <tr>
                                            <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">No items found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($items as $item): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                                            <?php $img = $item->image ?? $item['image'] ?? ''; ?>
                                                            <?php if ($img): ?>
                                                                <img src="<?= htmlspecialchars($img) ?>" alt="" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-400 text-sm"></i></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item->name ?? $item['name'] ?? '') ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars($item->sku ?? $item['sku'] ?? '') ?></td>
                                                <td class="px-5 py-4 text-gray-900 dark:text-white"><?= $item->quantity ?? $item['quantity'] ?? 1 ?></td>
                                                <td class="px-5 py-4 text-gray-900 dark:text-white">GH₵ <?= number_format($item->price ?? $item['price'] ?? 0, 2) ?></td>
                                                <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format(($item->price ?? $item['price'] ?? 0) * ($item->quantity ?? $item['quantity'] ?? 1), 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mt-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Order Status</h2>
                        <form action="/vendor/orders/<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?>/status" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                            <select name="status" class="w-full sm:w-auto px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                <i class="fas fa-sync-alt"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipping Information</h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($customerName) ?></p>
                            </div>
                            <?php if ($customerEmail): ?>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($customerEmail) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($customerPhone): ?>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($customerPhone) ?></p>
                                </div>
                            <?php endif; ?>
                            <hr class="border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Shipping Address</p>
                                <?php $address = $order->shipping_address ?? $order['shipping_address'] ?? []; ?>
                                <?php if (is_string($address)): ?>
                                    <p class="text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($address) ?></p>
                                <?php else: ?>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <?= htmlspecialchars(($address->address ?? $address['address'] ?? '') . ', ' . ($address->city ?? $address['city'] ?? '') . ', ' . ($address->state ?? $address['state'] ?? '') . ', ' . ($address->country ?? $address['country'] ?? '')) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
