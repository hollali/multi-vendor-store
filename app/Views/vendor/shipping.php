<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'shipping'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Shipping Rates</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage shipping costs and delivery estimates for each zone.</p>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg font-medium">
                    <i class="fas fa-check-circle text-xs"></i>
                    <?= count(array_filter($myRates ?? [], fn($r) => ($r->is_active ?? $r['is_active'] ?? 0))) ?> Active
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-lg font-medium">
                    <i class="fas fa-globe text-xs"></i>
                    <?= count($zones ?? []) ?> Zones
                </span>
            </div>
        </div>

        <?php if (empty($zones)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                    <i class="fas fa-truck text-amber-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No Shipping Zones Yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">Shipping zones haven't been configured by the administrator yet. Once zones are added, you'll be able to set your rates here.</p>
                <a href="/vendor/dashboard" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($zones as $zone): ?>
                    <?php
                    $zoneId = $zone->id ?? $zone['id'] ?? 0;
                    $zoneName = $zone->name ?? $zone['name'] ?? 'Unknown Zone';
                    $zoneType = $zone->type ?? $zone['type'] ?? 'local';
                    $zoneCountries = $zone->countries ?? $zone['countries'] ?? '';
                    $rate = null;
                    if (!empty($myRates)) {
                        foreach ($myRates as $r) {
                            if ((int)($r->zone_id ?? $r['zone_id'] ?? 0) === (int)$zoneId) {
                                $rate = $r;
                                break;
                            }
                        }
                    }
                    $hasRate = $rate !== null;
                    $baseRate = $hasRate ? (float)($rate->base_rate ?? $rate['base_rate'] ?? 0) : 0;
                    $ratePerKg = $hasRate ? (float)($rate->rate_per_kg ?? $rate['rate_per_kg'] ?? 0) : 0;
                    $freeShippingMin = $hasRate ? ($rate->free_shipping_min ?? $rate['free_shipping_min'] ?? '') : '';
                    $estMin = $hasRate ? (int)($rate->estimated_days_min ?? $rate['estimated_days_min'] ?? 3) : 3;
                    $estMax = $hasRate ? (int)($rate->estimated_days_max ?? $rate['estimated_days_max'] ?? 7) : 7;
                    $isActive = $hasRate ? ($rate->is_active ?? $rate['is_active'] ?? 1) : 1;

                    $typeIcon = $zoneType === 'local' ? 'fa-home' : ($zoneType === 'regional' ? 'fa-map-marker-alt' : 'fa-plane');
                    $typeLabel = $zoneType === 'local' ? 'Local' : ($zoneType === 'regional' ? 'Regional' : 'International');
                    $typeColor = $zoneType === 'local' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : ($zoneType === 'regional' ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400');
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden <?= !$hasRate ? 'opacity-75' : '' ?>">
                        <div class="px-5 lg:px-6 py-4 flex items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 <?= $typeColor ?>">
                                    <i class="fas <?= $typeIcon ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate"><?= htmlspecialchars($zoneName) ?></h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $typeColor ?>">
                                            <?= $typeLabel ?>
                                        </span>
                                        <?php if ($zoneCountries): ?>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($zoneCountries) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <?php if ($hasRate): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium <?= $isActive ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                                        <?= $isActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        <i class="fas fa-plus-circle"></i> Not Set
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="px-5 lg:px-6 py-4">
                            <form action="/vendor/shipping/save" method="POST">
                                <?= $csrf_field() ?>
                                <input type="hidden" name="zone_id" value="<?= $zoneId ?>">

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Base Rate</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-sm font-medium"><?= $geo_currency_symbol ?? 'GH₵' ?></span>
                                            <input type="number" name="base_rate" value="<?= $baseRate ?>" step="0.01" min="0" required
                                                   class="w-full pl-8 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                                   placeholder="0.00">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Fixed cost per order</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Per Kg</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-sm font-medium"><?= $geo_currency_symbol ?? 'GH₵' ?></span>
                                            <input type="number" name="rate_per_kg" value="<?= $ratePerKg ?>" step="0.01" min="0"
                                                   class="w-full pl-8 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                                   placeholder="0.00">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Additional per kg</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Free Shipping Min</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-sm font-medium"><?= $geo_currency_symbol ?? 'GH₵' ?></span>
                                            <input type="number" name="free_shipping_min" value="<?= htmlspecialchars($freeShippingMin !== '' ? $freeShippingMin : '') ?>" step="0.01" min="0" placeholder="—"
                                                   class="w-full pl-8 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Leave empty to disable</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Delivery Time</label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="estimated_days_min" value="<?= $estMin ?>" min="1" max="60" required
                                                   class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition text-center"
                                                   placeholder="Min">
                                            <span class="text-gray-400 text-xs font-medium">to</span>
                                            <input type="number" name="estimated_days_max" value="<?= $estMax ?>" min="1" max="90" required
                                                   class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition text-center"
                                                   placeholder="Max">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Business days</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?> class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-700"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                                    </label>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                        <i class="fas fa-save"></i>
                                        <?= $hasRate ? 'Update Rate' : 'Set Rate' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 lg:p-6 mt-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">How Shipping Works</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                            Each shipping zone lets you control costs for a specific region. The <strong>base rate</strong> is charged per order,
                            plus an additional <strong>per-kg fee</strong> based on product weight. Set a <strong>free shipping minimum</strong>
                            to waive the cost for large orders. Delivery estimates help set customer expectations.
                            Disable a zone's rate to stop shipping there temporarily.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>