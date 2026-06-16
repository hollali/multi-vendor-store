<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'users'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Manage Customers</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total: <strong><?= number_format($totalUsers ?? $total ?? 0) ?></strong> registered customers</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400">
                    </div>
                    <select id="statusFilter" class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                        <option value="">All Status</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="suspended" <?= ($_GET['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
            </div>

            <?php $users = $users ?? []; ?>
            <?php if (empty($users)): ?>
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No customers found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customers will appear here once they register.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Name</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Email</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Phone</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Orders</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Joined</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($users as $user): ?>
                                <?php
                                $status = $user->status ?? $user['status'] ?? 'active';
                                $statusStyles = ['active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'suspended' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                $isActive = $status === 'active';
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"><?= strtoupper(substr($user->name ?? $user['name'] ?? 'U', 0, 1)) ?></span>
                                            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($user->name ?? $user['name'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($user->email ?? $user['email'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($user->phone ?? $user['phone'] ?? $user->phone_number ?? $user['phone_number'] ?? '—') ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= number_format($user->orders_count ?? $user['orders_count'] ?? 0) ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['active'] ?>"><?= ucfirst($status) ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($user->created_at ?? $user['created_at'] ?? ''))) ?></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <?php if ($isActive): ?>
                                                <form action="/admin/users/<?= htmlspecialchars($user->id ?? $user['id'] ?? '') ?>/suspend" method="POST" class="inline">
                                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 text-xs font-medium rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition"><i class="fas fa-ban"></i> Suspend</button>
                                                </form>
                                            <?php else: ?>
                                                <form action="/admin/users/<?= htmlspecialchars($user->id ?? $user['id'] ?? '') ?>/activate" method="POST" class="inline">
                                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-medium rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition"><i class="fas fa-check"></i> Activate</button>
                                                </form>
                                            <?php endif; ?>
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
                        <a href="?page=<?= ($currentPage ?? 1) - 1 ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&status=<?= htmlspecialchars($_GET['status'] ?? '') ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i> Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&status=<?= htmlspecialchars($_GET['status'] ?? '') ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($currentPage ?? 1) == $i ? 'bg-primary-700 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if (($currentPage ?? 1) < $totalPages): ?>
                        <a href="?page=<?= ($currentPage ?? 1) + 1 ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&status=<?= htmlspecialchars($_GET['status'] ?? '') ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next <i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            const params = new URLSearchParams(window.location.search);
            if (searchInput.value) params.set('search', searchInput.value);
            else params.delete('search');
            params.set('page', '1');
            window.location.search = params.toString();
        }, 500);
    });

    statusFilter.addEventListener('change', function() {
        const params = new URLSearchParams(window.location.search);
        if (statusFilter.value) params.set('status', statusFilter.value);
        else params.delete('status');
        params.set('page', '1');
        window.location.search = params.toString();
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>