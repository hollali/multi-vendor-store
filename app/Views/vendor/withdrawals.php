<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'withdrawals'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Withdrawal History</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Request and track your withdrawals.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Withdrawal</h2>
            <form action="/vendor/withdrawals/store" method="POST" class="space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Amount (GH₵) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="1" id="amount" name="amount" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="0.00">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Available balance: GH₵ <?= number_format($availableBalance ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bank Name <span class="text-red-500">*</span></label>
                        <input type="text" id="bank_name" name="bank_name" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="e.g. Access Bank Ghana">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Account Number <span class="text-red-500">*</span></label>
                        <input type="text" id="account_number" name="account_number" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="0000000000">
                    </div>
                    <div>
                        <label for="account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Account Name <span class="text-red-500">*</span></label>
                        <input type="text" id="account_name" name="account_name" required
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="Full account name">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-paper-plane"></i> Submit Withdrawal Request
                </button>
            </form>
        </div>

        <?php $withdrawals = $withdrawals ?? []; ?>
        <?php if (empty($withdrawals)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                    <i class="fas fa-wallet text-indigo-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No withdrawal history</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your withdrawal requests will appear here.</p>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Withdrawal History</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Fee</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Net Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Bank</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($withdrawals as $wd): ?>
                                <?php
                                $wdStatus = $wd->status ?? $wd['status'] ?? 'pending';
                                $wdStatusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                $amount = $wd->amount ?? $wd['amount'] ?? 0;
                                $fee = $wd->fee ?? $wd['fee'] ?? 0;
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($amount, 2) ?></td>
                                    <td class="px-5 py-4 text-red-600 dark:text-red-400">- GH₵ <?= number_format($fee, 2) ?></td>
                                    <td class="px-5 py-4 font-semibold text-green-600 dark:text-green-400">GH₵ <?= number_format($amount - $fee, 2) ?></td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($wd->bank_name ?? $wd['bank_name'] ?? '') ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $wdStatusStyles[$wdStatus] ?? $wdStatusStyles['pending'] ?>"><?= ucfirst($wdStatus) ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($wd->created_at ?? $wd['created_at'] ?? ''))) ?></td>
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
