<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'overview'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Vendor Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Welcome back, <?= htmlspecialchars(explode(' ', $user->name ?? $user['name'] ?? 'Vendor')[0]) ?>! Here's your store overview.</p>
            </div>
            <a href="/vendor/products/create" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus-circle"></i> Add New Product
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            <?php
            $stats = $stats ?? [];
            $cards = [
                ['label' => 'Total Products', 'value' => $stats['total_products'] ?? 0, 'icon' => 'fa-boxes', 'from' => 'from-blue-600', 'to' => 'to-blue-400'],
                ['label' => 'Active Products', 'value' => $stats['active_products'] ?? 0, 'icon' => 'fa-check-circle', 'from' => 'from-green-600', 'to' => 'to-green-400'],
                ['label' => 'Total Orders', 'value' => $stats['total_orders'] ?? 0, 'icon' => 'fa-clipboard-list', 'from' => 'from-purple-600', 'to' => 'to-purple-400'],
                ['label' => 'Total Revenue', 'value' => 'GH₵ ' . number_format($stats['total_revenue'] ?? 0, 2), 'icon' => 'fa-credit-card', 'from' => 'from-orange-600', 'to' => 'to-orange-400'],
            ];
            ?>
            <?php foreach ($cards as $card): ?>
                <div class="relative overflow-hidden rounded-xl shadow-sm bg-white dark:bg-gray-800 p-5 group hover:shadow-md transition">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br <?= $card['from'] ?> <?= $card['to'] ?> opacity-10 rounded-bl-full"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= $card['label'] ?></p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= $card['value'] ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br <?= $card['from'] ?> <?= $card['to'] ?> flex items-center justify-center shadow-sm">
                            <i class="fas <?= $card['icon'] ?> text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Overview</h2>
                        <span class="text-xs text-gray-400">Last 6 months</span>
                    </div>
                    <div class="p-5">
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mt-6">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
                        <a href="/vendor/orders" class="text-sm text-primary-700 dark:text-primary-400 hover:underline font-medium">View All</a>
                    </div>
                    <?php $recentOrders = $recentOrders ?? $orders ?? []; ?>
                    <?php if (empty($recentOrders)): ?>
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-box text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No orders yet</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Orders will appear here once customers start purchasing.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Order #</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                                        <?php
                                        $status = $order->status ?? $order['status'] ?? 'pending';
                                        $statusStyles = ['pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300', 'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300', 'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                        $label = ucfirst($status);
                                        ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?></td>
                                            <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['pending'] ?>"><?= $label ?></span></td>
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></td>
                                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="space-y-2">
                        <?php
                        $actions = [
                            ['url' => '/vendor/products/create', 'icon' => 'fa-plus-circle', 'label' => 'Add Product', 'color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
                            ['url' => '/vendor/orders', 'icon' => 'fa-clipboard-list', 'label' => 'View Orders', 'color' => 'text-green-600 dark:text-green-400', 'bg' => 'bg-green-50 dark:bg-green-900/20'],
                            ['url' => '/vendor/store-settings', 'icon' => 'fa-store', 'label' => 'Store Settings', 'color' => 'text-purple-600 dark:text-purple-400', 'bg' => 'bg-purple-50 dark:bg-purple-900/20'],
                            ['url' => '/vendor/earnings', 'icon' => 'fa-chart-line', 'label' => 'View Earnings', 'color' => 'text-orange-600 dark:text-orange-400', 'bg' => 'bg-orange-50 dark:bg-orange-900/20'],
                            ['url' => '/vendor/coupons', 'icon' => 'fa-tag', 'label' => 'Manage Coupons', 'color' => 'text-pink-600 dark:text-pink-400', 'bg' => 'bg-pink-50 dark:bg-pink-900/20'],
                        ];
                        ?>
                        <?php foreach ($actions as $action): ?>
                            <a href="<?= $action['url'] ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">
                                <span class="w-9 h-9 rounded-lg <?= $action['bg'] ?> flex items-center justify-center <?= $action['color'] ?> group-hover:scale-105 transition">
                                    <i class="fas <?= $action['icon'] ?>"></i>
                                </span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= $action['label'] ?></span>
                                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 ml-auto text-xs"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = <?= json_encode($monthlyRevenue ?? []) ?>;
    const labels = monthlyData.length ? monthlyData.map(d => d.month ?? d.label ?? '') : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    const data = monthlyData.length ? monthlyData.map(d => parseFloat(d.total ?? d.revenue ?? 0)) : [0, 0, 0, 0, 0, 0];
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (GH₵)',
                data: data,
                backgroundColor: 'rgba(29, 78, 216, 0.7)',
                borderColor: 'rgba(29, 78, 216, 1)',
                borderWidth: 2,
                borderRadius: 6,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return 'GH₵' + value.toLocaleString(); }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
