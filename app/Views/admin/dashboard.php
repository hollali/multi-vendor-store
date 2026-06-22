<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'overview'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="mx-auto">
        <div class="mb-6 lg:mb-8">
            <h1 class="text-2xl lg:text-3xl xl:text-4xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <p class="text-sm lg:text-base text-gray-500 dark:text-gray-400 mt-1">Welcome back, <?= htmlspecialchars(explode(' ', $user->name ?? $user['name'] ?? 'Admin')[0]) ?>! Here's your platform overview.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 lg:gap-5 xl:gap-6 mb-6 lg:mb-8 xl:mb-10">
            <?php
            $adminCards = [
                ['label' => 'Total Users', 'value' => number_format($totalUsers ?? 0), 'icon' => 'fa-users', 'bg' => 'bg-blue-600'],
                ['label' => 'Total Vendors', 'value' => number_format($totalVendors ?? 0), 'icon' => 'fa-store', 'bg' => 'bg-purple-600'],
                ['label' => 'Total Products', 'value' => number_format(($totalProducts ?? 0) + ($pendingProducts ?? 0)), 'icon' => 'fa-boxes', 'bg' => 'bg-indigo-600'],
                ['label' => 'Total Orders', 'value' => number_format($totalOrders ?? 0), 'icon' => 'fa-clipboard-list', 'bg' => 'bg-green-600'],
                ['label' => 'Total Revenue', 'value' => ($geo_currency_symbol ?? 'GH₵') . ' ' . number_format($totalRevenue ?? 0, 2), 'icon' => 'fa-credit-card', 'bg' => 'bg-orange-600'],
                ['label' => 'Pending Withdrawals', 'value' => ($geo_currency_symbol ?? 'GH₵') . ' ' . number_format($pendingWithdrawals ?? 0, 2), 'icon' => 'fa-wallet', 'bg' => 'bg-red-600'],
            ];
            ?>
            <?php foreach ($adminCards as $card): ?>
                <div class="relative overflow-hidden rounded-xl shadow-sm bg-white dark:bg-gray-800 p-4 lg:p-5 xl:p-6 group hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= $card['label'] ?></p>
                            <p class="text-lg lg:text-xl font-bold text-gray-900 dark:text-white mt-1"><?= $card['value'] ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?= $card['bg'] ?> flex items-center justify-center shadow-sm">
                            <i class="fas <?= $card['icon'] ?> text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 mb-6 lg:mb-8 xl:mb-10">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 lg:px-6 xl:px-8 py-4 lg:py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white">Monthly Revenue</h2>
                    <span class="text-xs text-gray-400">Last 12 months</span>
                </div>
                <div class="p-5 lg:p-6 xl:p-8">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>

            <div class="space-y-6 lg:space-y-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6 xl:p-8">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white mb-4">Best Selling Products</h2>
                    <?php $bestSellers = $bestSellers ?? $stats['best_selling'] ?? []; ?>
                    <?php if (empty($bestSellers)): ?>
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6">No sales data yet</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($bestSellers, 0, 5) as $i => $product): ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-400 flex-shrink-0"><?= $i + 1 ?></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($product->name ?? $product['name'] ?? '') ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= number_format($product->total_qty ?? $product['total_qty'] ?? $product->sales_count ?? $product['sales_count'] ?? 0) ?> sold</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6 xl:p-8">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white mb-4">Top Vendors</h2>
                    <?php $topVendors = $topVendors ?? $stats['top_vendors'] ?? []; ?>
                    <?php if (empty($topVendors)): ?>
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6">No vendor data yet</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($topVendors, 0, 5) as $i => $vendor): ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-400 flex-shrink-0"><?= $i + 1 ?></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($vendor->store_name ?? $vendor['store_name'] ?? $vendor->vendor_name ?? $vendor['vendor_name'] ?? $vendor->name ?? $vendor['name'] ?? '') ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= ($geo_currency_symbol ?? 'GH₵') ?> <?= number_format($vendor->revenue ?? $vendor['revenue'] ?? 0, 2) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-5 lg:px-6 xl:px-8 py-4 lg:py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
                <a href="/admin/orders" class="text-sm text-primary-700 dark:text-primary-400 hover:underline font-medium">View All</a>
            </div>
            <?php $recentOrders = $recentOrders ?? $stats['recent_orders'] ?? []; ?>
            <?php if (empty($recentOrders)): ?>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-box text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No orders yet</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Order #</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Customer</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Total</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Payment</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach (array_slice($recentOrders, 0, 10) as $order): ?>
                                <?php
                                $orderStatus = $order->order_status ?? $order['order_status'] ?? $order->status ?? $order['status'] ?? 'pending';
                                $paymentStatus = $order->payment_status ?? $order['payment_status'] ?? 'pending';
                                $statusStyles = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                    'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $paymentStyles = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    'refunded' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
                                ];
                                $statusLabel = ucfirst($orderStatus);
                                $paymentLabel = ucfirst($paymentStatus);
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">#<?= htmlspecialchars($order->order_number ?? $order['order_number'] ?? $order->id ?? $order['id'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($order->customer_name ?? $order['customer_name'] ?? $order->user->name ?? $order['user']['name'] ?? 'Guest') ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= ($geo_currency_symbol ?? 'GH₵') ?> <?= number_format($order->total ?? $order['total'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$orderStatus] ?? $statusStyles['pending'] ?>"><?= $statusLabel ?></span></td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $paymentStyles[$paymentStatus] ?? $paymentStyles['pending'] ?>"><?= $paymentLabel ?></span></td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($order->created_at ?? $order['created_at'] ?? ''))) ?></td>
                                    <td class="px-5 py-4">
                                        <a href="/admin/orders/<?= htmlspecialchars($order->id ?? $order['id'] ?? '') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 text-xs font-medium rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/40 transition"><i class="fas fa-eye"></i> View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <a href="/admin/users" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition border border-gray-200 dark:border-gray-700 group">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-105 transition"><i class="fas fa-users"></i></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Users</span>
                </div>
            </a>
            <a href="/admin/vendors" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition border border-gray-200 dark:border-gray-700 group">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-105 transition"><i class="fas fa-store"></i></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Vendors</span>
                </div>
            </a>
            <a href="/admin/products" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition border border-gray-200 dark:border-gray-700 group">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition"><i class="fas fa-boxes"></i></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Products</span>
                </div>
            </a>
            <a href="/admin/categories" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition border border-gray-200 dark:border-gray-700 group">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:scale-105 transition"><i class="fas fa-tags"></i></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Categories</span>
                </div>
            </a>
            <a href="/admin/orders" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition border border-gray-200 dark:border-gray-700 group">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400 group-hover:scale-105 transition"><i class="fas fa-clipboard-list"></i></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Orders</span>
                </div>
            </a>
            <a href="/admin/settings" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition border border-gray-200 dark:border-gray-700 group">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center text-gray-600 dark:text-gray-400 group-hover:scale-105 transition"><i class="fas fa-cog"></i></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Platform Settings</span>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = <?= json_encode($monthlyRevenue ?? $stats['monthly_revenue'] ?? []) ?>;
    const labels = monthlyData.length ? monthlyData.map(d => d.month ?? d.label ?? '') : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const data = monthlyData.length ? monthlyData.map(d => parseFloat(d.revenue ?? d.total ?? 0)) : [0,0,0,0,0,0,0,0,0,0,0,0];
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (<?= $geo_currency_symbol ?? 'GH₵' ?>)',
                data: data,
                backgroundColor: 'rgba(29, 78, 216, 0.1)',
                borderColor: 'rgba(29, 78, 216, 1)',
                borderWidth: 3,
                pointBackgroundColor: 'rgba(29, 78, 216, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                tension: 0.4,
                fill: true,
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
                        callback: function(value) { return '<?= $geo_currency_symbol ?? 'GH₵' ?>' + value.toLocaleString(); }
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
