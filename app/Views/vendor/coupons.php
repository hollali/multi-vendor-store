<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'coupons'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Coupons</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create and manage discount coupons for your products.</p>
            </div>
            <button onclick="document.getElementById('coupon-form-modal').classList.toggle('hidden')" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus-circle"></i> Create Coupon
            </button>
        </div>

        <div id="coupon-form-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Create Coupon</h2>
                    <button onclick="document.getElementById('coupon-form-modal').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-times"></i></button>
                </div>
                <form action="/vendor/coupons/store" method="POST" class="p-5 space-y-4">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Coupon Code <span class="text-red-500">*</span></label>
                        <input type="text" id="code" name="code" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition uppercase"
                               placeholder="e.g. SUMMER20">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type <span class="text-red-500">*</span></label>
                            <select id="type" name="type" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Value <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" id="value" name="value" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="0.00">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="min_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Min. Order (GH₵)</label>
                            <input type="number" step="0.01" min="0" id="min_order" name="min_order"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label for="max_discount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Max Discount (GH₵)</label>
                            <input type="number" step="0.01" min="0" id="max_discount" name="max_discount"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="0.00">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usage Limit</label>
                            <input type="number" min="0" id="usage_limit" name="usage_limit"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="Unlimited">
                        </div>
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Expiry Date</label>
                            <input type="date" id="expiry_date" name="expiry_date"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 bg-primary-700 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Create Coupon
                    </button>
                </form>
            </div>
        </div>

        <?php $coupons = $coupons ?? []; ?>
        <?php if (empty($coupons)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center">
                    <i class="fas fa-tag text-pink-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No coupons yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Create your first coupon to start offering discounts.</p>
                <button onclick="document.getElementById('coupon-form-modal').classList.toggle('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-plus-circle"></i> Create Your First Coupon
                </button>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Code</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Type</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Value</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Used / Limit</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Expires</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($coupons as $coupon): ?>
                                <?php
                                $cStatus = $coupon->status ?? $coupon['status'] ?? 'active';
                                $cType = $coupon->type ?? $coupon['type'] ?? 'fixed';
                                $used = $coupon->used_count ?? $coupon['used_count'] ?? 0;
                                $limit = $coupon->usage_limit ?? $coupon['usage_limit'] ?? 0;
                                $expiry = $coupon->expiry_date ?? $coupon['expiry_date'] ?? '';
                                $isExpired = $expiry && strtotime($expiry) < time();
                                $statusLabel = $isExpired ? 'Expired' : ($cStatus === 'active' ? 'Active' : 'Inactive');
                                $statusStyle = $isExpired ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : ($cStatus === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300');
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-mono font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($coupon->code ?? $coupon['code'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 capitalize"><?= $cType ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= $cType === 'percentage' ? $coupon->value . '%' : 'GH₵ ' . number_format($coupon->value ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= $used ?> / <?= $limit > 0 ? $limit : '∞' ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyle ?>"><?= $statusLabel ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= $expiry ? date('M d, Y', strtotime($expiry)) : 'Never' ?></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <form action="/vendor/coupons/<?= htmlspecialchars($coupon->id ?? $coupon['id'] ?? '') ?>/toggle" method="POST" class="inline">
                                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                                    <i class="fas <?= $cStatus === 'active' ? 'fa-pause' : 'fa-play' ?>"></i> <?= $cStatus === 'active' ? 'Pause' : 'Activate' ?>
                                                </button>
                                            </form>
                                            <form action="/vendor/coupons/<?= htmlspecialchars($coupon->id ?? $coupon['id'] ?? '') ?>/delete" method="POST" onsubmit="return confirm('Delete this coupon?')" class="inline">
                                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
