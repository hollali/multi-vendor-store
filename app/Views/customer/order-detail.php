<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'orders'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <?php $order = $order ?? []; ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <a href="/orders" class="text-sm text-primary-700 dark:text-primary-400 hover:underline mb-2 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Orders</a>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Order #<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Placed on <?= htmlspecialchars(date('F d, Y \a\t h:i A', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></p>
            </div>
            <?php
            $status = $order->status ?? $order['status'] ?? 'pending';
            $statusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300', 'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
            $label = ucfirst($status);
            ?>
            <span class="mt-3 sm:mt-0 inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium <?= $statusStyles[$status] ?? $statusStyles['pending'] ?>"><?= $label ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ordered Items</h2>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php $items = $order->items ?? $order['items'] ?? []; ?>
                        <?php foreach ($items as $item): ?>
                            <div class="px-5 py-4 flex items-center gap-4">
                                <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                    <img src="<?= htmlspecialchars($item->image ?? $item['image'] ?? '/assets/img/placeholder.png') ?>" alt="<?= htmlspecialchars($item->name ?? $item['name'] ?? 'Product') ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($item->name ?? $item['name'] ?? '') ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Qty: <?= $item->quantity ?? $item['quantity'] ?? 1 ?> &times; GHS <?= number_format($item->price ?? $item['price'] ?? 0, 2) ?></p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">GHS <?= number_format(($item->quantity ?? $item['quantity'] ?? 1) * ($item->price ?? $item['price'] ?? 0), 2) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php $tracking = $order->tracking_number ?? $order['tracking_number'] ?? ''; ?>
                <?php if ($tracking): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Tracking Information</h2>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <i class="fas fa-truck text-blue-600 dark:text-blue-400 text-xl"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Tracking Number</p>
                                <p class="text-sm text-blue-700 dark:text-blue-400 font-mono"><?= htmlspecialchars($tracking) ?></p>
                            </div>
                            <a href="#" class="ml-auto text-sm text-primary-700 dark:text-primary-400 hover:underline font-medium">Track <i class="fas fa-external-link-alt text-[10px]"></i></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span class="text-gray-900 dark:text-white">GHS <?= number_format($order->subtotal ?? $order['subtotal'] ?? 0, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span class="text-gray-900 dark:text-white">GHS <?= number_format($order->shipping ?? $order['shipping'] ?? 0, 2) ?></span>
                        </div>
                        <?php if (($order->discount ?? $order['discount'] ?? 0) > 0): ?>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Discount</span>
                                <span class="text-green-600 dark:text-green-400">- GHS <?= number_format($order->discount ?? $order['discount'] ?? 0, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <hr class="border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white">
                            <span>Total</span>
                            <span>GHS <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></span>
                        </div>
                    </div>

                    <?php
                    $paymentStatus = $order->payment_status ?? $order['payment_status'] ?? 'pending';
                    $psStyles = ['paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                    $psLabel = ucfirst($paymentStatus);
                    ?>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Payment Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $psStyles[$paymentStatus] ?? $psStyles['pending'] ?>"><?= $psLabel ?></span>
                    </div>
                </div>

                <?php $address = $order->shipping_address ?? $order['shipping_address'] ?? []; ?>
                <?php if (!empty($address)): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Shipping Address</h2>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-gray-400 mt-0.5"></i>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($address->full_name ?? $address['full_name'] ?? '') ?></p>
                                <p><?= htmlspecialchars($address->street_address ?? $address['street_address'] ?? '') ?></p>
                                <p><?= htmlspecialchars($address->city ?? $address['city'] ?? '') ?>, <?= htmlspecialchars($address->state ?? $address['state'] ?? '') ?> <?= htmlspecialchars($address->postal_code ?? $address['postal_code'] ?? '') ?></p>
                                <p><?= htmlspecialchars($address->country ?? $address['country'] ?? 'Ghana') ?></p>
                                <p class="mt-1">Phone: <?= htmlspecialchars($address->phone ?? $address['phone'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="#" onclick="window.print()" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-file-invoice"></i> Download Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
