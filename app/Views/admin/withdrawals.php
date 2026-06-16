<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'withdrawals'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Withdrawal Requests</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage vendor payout requests.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <?php $withdrawals = $withdrawals ?? []; ?>
            <?php if (empty($withdrawals)): ?>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-wallet text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No withdrawal requests</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending withdrawals will appear here.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Vendor</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Bank Details</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Fee</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Net Amount</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($withdrawals as $w): ?>
                                <?php
                                $status = $w->status ?? $w['status'] ?? 'pending';
                                $statusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-sm flex-shrink-0">
                                                <i class="fas fa-store"></i>
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($w->vendor_name ?? $w['vendor_name'] ?? $w->vendor->store_name ?? $w['vendor']['store_name'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-xs">
                                            <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($w->bank_name ?? $w['bank_name'] ?? '') ?></p>
                                            <p class="text-gray-500 dark:text-gray-400">****<?= htmlspecialchars(substr($w->account_number ?? $w['account_number'] ?? '', -4)) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($w->amount ?? $w['amount'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400">GH₵ <?= number_format($w->fee ?? $w['fee'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($w->net_amount ?? $w['net_amount'] ?? ($w->amount ?? 0) - ($w->fee ?? 0), 2) ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['pending'] ?>"><?= ucfirst($status) ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap"><?= htmlspecialchars(date('M d, Y', strtotime($w->created_at ?? $w['created_at'] ?? ''))) ?></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <button onclick="openStatusModal(<?= htmlspecialchars($w->id ?? $w['id'] ?? 0) ?>, 'processing')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition"><i class="fas fa-spinner"></i> Process</button>
                                            <button onclick="openStatusModal(<?= htmlspecialchars($w->id ?? $w['id'] ?? 0) ?>, 'completed')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-medium rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition"><i class="fas fa-check"></i> Complete</button>
                                            <button onclick="openStatusModal(<?= htmlspecialchars($w->id ?? $w['id'] ?? 0) ?>, 'failed')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-times"></i> Fail</button>
                                        </div>
                                    </td>
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
                        <a href="?page=<?= ($currentPage ?? 1) - 1 ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i> Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($currentPage ?? 1) == $i ? 'bg-primary-700 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if (($currentPage ?? 1) < $totalPages): ?>
                        <a href="?page=<?= ($currentPage ?? 1) + 1 ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next <i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="statusModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeStatusModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Update Withdrawal Status</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Set status to <strong id="statusLabel" class="text-gray-900 dark:text-white"></strong>?</p>
                <form id="statusForm" method="POST" class="flex gap-3 justify-center">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <input type="hidden" name="status" id="statusInput" value="">
                    <button type="button" onclick="closeStatusModal()" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-check mr-1.5"></i> Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openStatusModal(id, status) {
    document.getElementById('statusLabel').textContent = status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('statusInput').value = status;
    document.getElementById('statusForm').action = '/admin/withdrawals/' + id + '/update-status';
    document.getElementById('statusModal').classList.remove('hidden');
}
function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>