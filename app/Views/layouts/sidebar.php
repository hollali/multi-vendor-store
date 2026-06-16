<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 lg:w-80 xl:w-96 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 h-screen overflow-y-auto flex-shrink-0 transition-transform duration-300 ease-in-out -translate-x-full md:sticky md:top-0 md:translate-x-0 md:flex md:flex-col">
    <div class="p-4 lg:p-6 xl:p-8 border-b border-gray-200 dark:border-gray-800">
        <a href="/" class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-primary-700 to-primary-500 rounded-lg shadow-md">
                <i class="fas fa-bolt text-white text-sm"></i>
            </span>
            <span class="text-lg font-extrabold bg-gradient-to-r from-primary-700 to-accent-600 bg-clip-text text-transparent">Celer Market</span>
        </a>
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm flex-shrink-0">
                <?= strtoupper(substr($user->name ?? $user['name'] ?? 'U', 0, 1)) ?>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($user->email ?? $user['email'] ?? '') ?></p>
            </div>
        </div>
    </div>

    <nav class="flex-1 p-3 lg:p-4 xl:p-5 space-y-1 overflow-y-auto">
        <?php
        $role = $userRole ?? $session->getUserRole() ?? 'customer';
        $activeMenu = $activeMenu ?? 'overview';

        function isActive($menu, $active): string {
            return $menu === $active ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-semibold border-l-4 border-primary-700 dark:border-primary-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent';
        }
        ?>

        <?php if ($role === 'customer'): ?>
            <div class="text-[11px] uppercase tracking-widest text-gray-400 dark:text-gray-600 font-semibold px-3 pt-4 pb-2">Customer Dashboard</div>
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('overview', $activeMenu) ?>">
                <i class="fas fa-th-large w-4 text-center"></i><span>Overview</span>
            </a>
            <a href="/orders" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('orders', $activeMenu) ?>">
                <i class="fas fa-box w-4 text-center"></i><span>Orders</span>
            </a>
            <a href="/wishlist" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('wishlist', $activeMenu) ?>">
                <i class="far fa-heart w-4 text-center"></i><span>Wishlist</span>
            </a>
            <a href="/addresses" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('addresses', $activeMenu) ?>">
                <i class="fas fa-map-marker-alt w-4 text-center"></i><span>Addresses</span>
            </a>
            <a href="/reviews" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('reviews', $activeMenu) ?>">
                <i class="fas fa-star w-4 text-center"></i><span>Reviews</span>
            </a>
            <a href="/dashboard/notifications" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('notifications', $activeMenu) ?>">
                <i class="fas fa-bell w-4 text-center"></i><span>Notifications</span>
            </a>
            <hr class="my-2 border-gray-100 dark:border-gray-800">
            <a href="/profile" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('profile', $activeMenu) ?>">
                <i class="fas fa-user-cog w-4 text-center"></i><span>Profile Settings</span>
            </a>
            <a href="/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 border-l-4 border-transparent hover:border-red-500">
                <i class="fas fa-sign-out-alt w-4 text-center"></i><span>Logout</span>
            </a>

        <?php elseif ($role === 'vendor'): ?>
            <div class="text-[11px] uppercase tracking-widest text-gray-400 dark:text-gray-600 font-semibold px-3 pt-4 pb-2">Vendor Dashboard</div>
            <a href="/vendor/dashboard" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('overview', $activeMenu) ?>">
                <i class="fas fa-th-large w-4 text-center"></i><span>Dashboard</span>
            </a>
            <a href="/vendor/products" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('products', $activeMenu) ?>">
                <i class="fas fa-boxes w-4 text-center"></i><span>Products</span>
            </a>
            <a href="/vendor/products/create" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('add-product', $activeMenu) ?>">
                <i class="fas fa-plus-circle w-4 text-center"></i><span>Add New Product</span>
            </a>
            <a href="/vendor/orders" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('orders', $activeMenu) ?>">
                <i class="fas fa-clipboard-list w-4 text-center"></i><span>Orders</span>
            </a>
            <a href="/vendor/reviews" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('reviews', $activeMenu) ?>">
                <i class="fas fa-star w-4 text-center"></i><span>Reviews</span>
            </a>
            <a href="/vendor/coupons" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('coupons', $activeMenu) ?>">
                <i class="fas fa-tag w-4 text-center"></i><span>Coupons</span>
            </a>
            <a href="/vendor/earnings" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('earnings', $activeMenu) ?>">
                <i class="fas fa-chart-line w-4 text-center"></i><span>Earnings</span>
            </a>
            <a href="/vendor/withdrawals" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('withdrawals', $activeMenu) ?>">
                <i class="fas fa-wallet w-4 text-center"></i><span>Withdrawals</span>
            </a>
            <hr class="my-2 border-gray-100 dark:border-gray-800">
            <a href="/vendor/store-settings" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('store-settings', $activeMenu) ?>">
                <i class="fas fa-store w-4 text-center"></i><span>Store Settings</span>
            </a>
            <a href="/vendor/notifications" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('notifications', $activeMenu) ?>">
                <i class="fas fa-bell w-4 text-center"></i><span>Notifications</span>
            </a>
            <a href="/profile" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('profile', $activeMenu) ?>">
                <i class="fas fa-user-cog w-4 text-center"></i><span>Profile</span>
            </a>
            <hr class="my-2 border-gray-100 dark:border-gray-800">
            <a href="/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 border-l-4 border-transparent hover:border-red-500">
                <i class="fas fa-sign-out-alt w-4 text-center"></i><span>Logout</span>
            </a>
        <?php elseif ($role === 'admin'): ?>
            <div class="text-[11px] uppercase tracking-widest text-gray-400 dark:text-gray-600 font-semibold px-3 pt-4 pb-2">Admin Dashboard</div>
            <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('overview', $activeMenu) ?>">
                <i class="fas fa-th-large w-4 text-center"></i><span>Dashboard</span>
            </a>
            <a href="/admin/users" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('users', $activeMenu) ?>">
                <i class="fas fa-users w-4 text-center"></i><span>Users</span>
            </a>
            <a href="/admin/vendors" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('vendors', $activeMenu) ?>">
                <i class="fas fa-store w-4 text-center"></i><span>Vendors</span>
            </a>
            <a href="/admin/products" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('products', $activeMenu) ?>">
                <i class="fas fa-boxes w-4 text-center"></i><span>Products</span>
            </a>
            <a href="/admin/categories" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('categories', $activeMenu) ?>">
                <i class="fas fa-tags w-4 text-center"></i><span>Categories</span>
            </a>
            <a href="/admin/brands" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('brands', $activeMenu) ?>">
                <i class="fas fa-copyright w-4 text-center"></i><span>Brands</span>
            </a>
            <a href="/admin/orders" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('orders', $activeMenu) ?>">
                <i class="fas fa-clipboard-list w-4 text-center"></i><span>Orders</span>
            </a>
            <a href="/admin/transactions" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('transactions', $activeMenu) ?>">
                <i class="fas fa-credit-card w-4 text-center"></i><span>Transactions</span>
            </a>
            <a href="/admin/withdrawals" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('withdrawals', $activeMenu) ?>">
                <i class="fas fa-wallet w-4 text-center"></i><span>Withdrawals</span>
            </a>
            <a href="/admin/banners" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('banners', $activeMenu) ?>">
                <i class="fas fa-image w-4 text-center"></i><span>Banners</span>
            </a>
            <hr class="my-2 border-gray-100 dark:border-gray-800">
            <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('settings', $activeMenu) ?>">
                <i class="fas fa-cog w-4 text-center"></i><span>Settings</span>
            </a>
            <a href="/admin/notifications" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('notifications', $activeMenu) ?>">
                <i class="fas fa-bell w-4 text-center"></i><span>Notifications</span>
            </a>
            <a href="/admin/profile" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition <?= isActive('profile', $activeMenu) ?>">
                <i class="fas fa-user-cog w-4 text-center"></i><span>Profile</span>
            </a>
            <hr class="my-2 border-gray-100 dark:border-gray-800">
            <a href="/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-r-lg transition text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 border-l-4 border-transparent hover:border-red-500">
                <i class="fas fa-sign-out-alt w-4 text-center"></i><span>Logout</span>
            </a>
        <?php endif; ?>
    </nav>
</aside>

<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden"></div>

<button id="sidebarToggle" class="fixed bottom-4 left-4 z-50 md:hidden w-12 h-12 bg-primary-700 hover:bg-primary-800 text-white rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105 active:scale-95" aria-label="Toggle sidebar">
    <i class="fas fa-bars"></i>
</button>
