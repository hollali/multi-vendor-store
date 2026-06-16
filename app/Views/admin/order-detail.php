<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'orders'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <a href="/admin/orders" class="text-sm text-primary-700 dark:text-primary-400 hover:underline mb-1 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Orders</a>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Order #<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Placed on <?= htmlspecialchars(date('F d, Y \a\t h:i A', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></p>
            </div>
        </div>

        <?php $order = $order ?? []; ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h2>
                    </div>
                    <?php $items = $order->items ?? $order['items'] ?? []; ?>
                    <?php if (empty($items)): ?>
                        <div class="p-8 text-center text-sm text-gray-400">No items found.</div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Product</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Vendor</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Qty</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Price</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Commission</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Vendor Earnings</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($items as $item): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <?php $img = $item->image ?? $item['image'] ?? $item->product->image ?? $item['product']['image'] ?? ''; ?>
                                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                                        <?php if ($img): ?>
                                                            <img src="<?= htmlspecialchars($img) ?>" alt="" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="w-full h-full flex items-center justify-center"><i class="fas fa-box text-gray-400 text-sm"></i></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item->name ?? $item['name'] ?? $item->product->name ?? $item['product']['name'] ?? '') ?></span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($item->vendor_name ?? $item['vendor_name'] ?? $item->vendor->store_name ?? $item['vendor']['store_name'] ?? '') ?></td>
                                            <td class="px-5 py-4 text-gray-900 dark:text-white"><?= $item->quantity ?? $item['quantity'] ?? 0 ?></td>
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($item->price ?? $item['price'] ?? 0, 2) ?></td>
                                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400">GH₵ <?= number_format($item->commission ?? $item['commission'] ?? 0, 2) ?></td>
                                            <td class="px-5 py-4 font-medium text-green-600 dark:text-green-400">GH₵ <?= number_format($item->vendor_earnings ?? $item['vendor_earnings'] ?? ($item->vendor_amount ?? $item['vendor_amount'] ?? 0), 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Update</h2>
                    <form action="/admin/orders/<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?>/update-status" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order Status</label>
                            <select name="order_status" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                                <?php $currentStatus = $order->status ?? $order['status'] ?? 'pending'; ?>
                                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $currentStatus === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Status</label>
                            <select name="payment_status" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                                <?php $currentPayment = $order->payment_status ?? $order['payment_status'] ?? 'unpaid'; ?>
                                <?php foreach (['unpaid', 'paid', 'refunded', 'partial'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $currentPayment === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-save mr-1.5"></i> Update Status</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</p>
                            <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order->customer_name ?? $order['customer_name'] ?? $order->user->name ?? $order['user']['name'] ?? 'Guest') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</p>
                            <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order->customer_email ?? $order['customer_email'] ?? $order->user->email ?? $order['user']['email'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</p>
                            <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order->customer_phone ?? $order['customer_phone'] ?? $order->user->phone ?? $order['user']['phone'] ?? '—') ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipping Address</h2>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <?php $address = $order->shipping_address ?? $order['shipping_address'] ?? []; ?>
                        <?php if (is_string($address)): ?>
                            <p><?= htmlspecialchars($address) ?></p>
                        <?php else: ?>
                            <p><?= htmlspecialchars($address->address ?? $address['address'] ?? $address->line1 ?? $address['line1'] ?? '') ?></p>
                            <p><?= htmlspecialchars($address->city ?? $address['city'] ?? '') ?>, <?= htmlspecialchars($address->state ?? $address['state'] ?? '') ?> <?= htmlspecialchars($address->zip ?? $address['zip'] ?? '') ?></p>
                            <p><?= htmlspecialchars($address->country ?? $address['country'] ?? 'Ghana') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</p>
                            <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order->payment_method ?? $order['payment_method'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaction ID</p>
                            <p class="font-medium text-gray-900 dark:text-white font-mono"><?= htmlspecialchars($order->transaction_id ?? $order['transaction_id'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($order->payment_status ?? $order['payment_status'] ?? 'unpaid') === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' ?>"><?= ucfirst($order->payment_status ?? $order['payment_status'] ?? 'unpaid') ?></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Total Breakdown</h2>
                    <div class="space-y-3 text-sm">
                        <?php
                        $subtotal = $order->subtotal ?? $order['subtotal'] ?? 0;
                        $discount = $order->discount ?? $order['discount'] ?? 0;
                        $shipping = $order->shipping ?? $order['shipping'] ?? $order->shipping_fee ?? $order['shipping_fee'] ?? 0;
                        $tax = $order->tax ?? $order['tax'] ?? 0;
                        $total = $order->total ?? $order['total'] ?? 0;
                        ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <?php if ($discount > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Discount</span>
                                <span class="font-medium text-red-600 dark:text-red-400">-GH₵ <?= number_format($discount, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Shipping</span>
                            <span class="font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($shipping, 2) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Tax</span>
                            <span class="font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($tax, 2) ?></span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-base">
                            <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="font-bold text-gray-900 dark:text-white">GH₵ <?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>