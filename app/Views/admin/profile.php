<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'profile'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Admin Profile</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your account information</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
            </div>
            <form action="/admin/profile" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">

                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-3xl font-bold ring-4 ring-gray-50 dark:ring-gray-800">
                        <?= strtoupper(substr($profileUser->first_name ?? $profileUser['first_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars(($profileUser->first_name ?? $profileUser['first_name'] ?? '') . ' ' . ($profileUser->last_name ?? $profileUser['last_name'] ?? '')) ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($profileUser->email ?? $profileUser['email'] ?? '') ?></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Administrator</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($profileUser->first_name ?? $profileUser['first_name'] ?? '') ?>" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($profileUser->last_name ?? $profileUser['last_name'] ?? '') ?>" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" value="<?= htmlspecialchars($profileUser->email ?? $profileUser['email'] ?? '') ?>" readonly class="w-full px-3 py-2.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($profileUser->phone ?? $profileUser['phone'] ?? '') ?>" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-save mr-1"></i> Save Changes</button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Leave blank if you don't want to change it</p>
            </div>
            <form action="/admin/profile/password" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                    <input type="password" name="current_password" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="new_password" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-gray-800 dark:bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-key mr-1"></i> Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
