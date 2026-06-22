<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'earnings'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Earnings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track your revenue and payouts.</p>
            </div>
            <?php if (($availableBalance ?? 0) > 0): ?>
                <a href="/vendor/withdrawals" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-wallet"></i> Request Withdrawal
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            <?php
            $earningsStats = $earningsStats ?? [];
            $cards = [
                ['label' => 'Total Earned', 'value' => 'GH₵ ' . number_format($earningsStats['total_earned'] ?? 0, 2), 'icon' => 'fa-credit-card', 'bg' => 'bg-blue-600'],
                ['label' => 'Pending', 'value' => 'GH₵ ' . number_format($earningsStats['pending'] ?? 0, 2), 'icon' => 'fa-clock', 'bg' => 'bg-yellow-600'],
                ['label' => 'Available for Withdrawal', 'value' => 'GH₵ ' . number_format($earningsStats['available'] ?? 0, 2), 'icon' => 'fa-check-circle', 'bg' => 'bg-green-600'],
                ['label' => 'Withdrawn', 'value' => 'GH₵ ' . number_format($earningsStats['withdrawn'] ?? 0, 2), 'icon' => 'fa-history', 'bg' => 'bg-purple-600'],
            ];
            ?>
            <?php foreach ($cards as $card): ?>
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Earnings History</h2>
            </div>
            <?php $earnings = $earnings ?? []; ?>
            <?php if (empty($earnings)): ?>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-chart-line text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No earnings recorded yet.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Order #</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Product</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Commission</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Net Earnings</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($earnings as $earning): ?>
                                <?php
                                $eStatus = $earning->status ?? $earning['status'] ?? 'pending';
                                $eStatusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'available' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'paid' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'];
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($earning->order_id ?? $earning['order_id'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($earning->product_name ?? $earning['product_name'] ?? '') ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($earning->amount ?? $earning['amount'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 text-red-600 dark:text-red-400">- GH₵ <?= number_format($earning->commission ?? $earning['commission'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 font-semibold text-green-600 dark:text-green-400">GH₵ <?= number_format(($earning->amount ?? $earning['amount'] ?? 0) - ($earning->commission ?? $earning['commission'] ?? 0), 2) ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($earning->created_at ?? $earning['created_at'] ?? ''))) ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $eStatusStyles[$eStatus] ?? $eStatusStyles['pending'] ?>"><?= ucfirst($eStatus) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
