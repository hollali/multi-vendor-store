<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'transactions'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Platform Transactions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor all financial transactions on the platform.</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-5">
            <?php
            $currentFilter = $_GET['type'] ?? 'all';
            $filters = [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'payment', 'label' => 'Payment'],
                ['key' => 'refund', 'label' => 'Refund'],
                ['key' => 'commission', 'label' => 'Commission'],
                ['key' => 'withdrawal', 'label' => 'Withdrawal'],
                ['key' => 'payout', 'label' => 'Payout'],
            ];
            ?>
            <?php foreach ($filters as $f): ?>
                <a href="?type=<?= $f['key'] ?>"
                   class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition <?= $currentFilter === $f['key'] ? 'bg-primary-700 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' ?>">
                    <?= $f['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <?php $transactions = $transactions ?? []; ?>
            <?php if (empty($transactions)): ?>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-credit-card text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No transactions yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Transactions will appear once orders are placed.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Reference</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">User</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Type</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Fee</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Net Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Description</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($transactions as $txn): ?>
                                <?php
                                $type = $txn->type ?? $txn['type'] ?? 'payment';
                                $typeStyles = ['payment' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'refund' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300', 'commission' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'withdrawal' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'payout' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'];
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-mono text-xs font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($txn->reference ?? $txn['reference'] ?? $txn->transaction_id ?? $txn['transaction_id'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($txn->user_name ?? $txn['user_name'] ?? $txn->user->name ?? $txn['user']['name'] ?? '') ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeStyles[$type] ?? $typeStyles['payment'] ?>"><?= ucfirst($type) ?></span></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($txn->amount ?? $txn['amount'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400">GH₵ <?= number_format($txn->fee ?? $txn['fee'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($txn->net_amount ?? $txn['net_amount'] ?? ($txn->amount ?? 0) - ($txn->fee ?? 0), 2) ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 truncate max-w-[200px]"><?= htmlspecialchars($txn->description ?? $txn['description'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap"><?= htmlspecialchars(date('M d, Y', strtotime($txn->created_at ?? $txn['created_at'] ?? ''))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="flex items-center justify-between mt-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Page <?= $currentPage ?? 1 ?> of <?= $totalPages ?></p>
                <div class="flex gap-2">
                    <?php if (($currentPage ?? 1) > 1): ?>
                        <a href="?page=<?= ($currentPage ?? 1) - 1 ?>&type=<?= $currentFilter ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i> Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&type=<?= $currentFilter ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($currentPage ?? 1) == $i ? 'bg-primary-700 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if (($currentPage ?? 1) < $totalPages): ?>
                        <a href="?page=<?= ($currentPage ?? 1) + 1 ?>&type=<?= $currentFilter ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next <i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>