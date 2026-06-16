<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'store-settings'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Store Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your store information and branding.</p>
        </div>

        <form action="/vendor/store-settings/update" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Branding</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Store Logo</label>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700 flex-shrink-0" id="logo-preview">
                                    <?php $logo = $store->logo ?? $store['logo'] ?? ''; ?>
                                    <?php if ($logo): ?>
                                        <img src="<?= htmlspecialchars($logo) ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <i class="fas fa-store text-gray-400 text-2xl"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <label for="logo" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                        <i class="fas fa-upload"></i> Upload Logo
                                    </label>
                                    <input type="file" id="logo" name="logo" accept="image/*" class="hidden" onchange="previewImage(this, 'logo-preview')">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PNG, JPG, WebP. 200x200px recommended.</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Store Banner</label>
                            <div class="flex items-center gap-4">
                                <div class="w-full h-24 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700" id="banner-preview">
                                    <?php $banner = $store->banner ?? $store['banner'] ?? ''; ?>
                                    <?php if ($banner): ?>
                                        <img src="<?= htmlspecialchars($banner) ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <label for="banner" class="mt-2 cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <i class="fas fa-upload"></i> Upload Banner
                            </label>
                            <input type="file" id="banner" name="banner" accept="image/*" class="hidden" onchange="previewImage(this, 'banner-preview')">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">1200x400px recommended.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="store_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Store Name <span class="text-red-500">*</span></label>
                            <input type="text" id="store_name" name="store_name" value="<?= htmlspecialchars($store->name ?? $store['name'] ?? '') ?>" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="Your store name">
                        </div>
                        <div>
                            <label for="store_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Store Email <span class="text-red-500">*</span></label>
                            <input type="email" id="store_email" name="store_email" value="<?= htmlspecialchars($store->email ?? $store['email'] ?? '') ?>" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="store@example.com">
                        </div>
                        <div>
                            <label for="store_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Store Phone <span class="text-red-500">*</span></label>
                            <input type="tel" id="store_phone" name="store_phone" value="<?= htmlspecialchars($store->phone ?? $store['phone'] ?? '') ?>" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="+233 XX XXX XXXX">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">About Your Store</h2>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="5"
                              class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition resize-y"
                              placeholder="Tell customers about your store..."><?= htmlspecialchars($store->description ?? $store['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($store->address ?? $store['address'] ?? '') ?>"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="Street address">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">City</label>
                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($store->city ?? $store['city'] ?? '') ?>"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="e.g. Accra">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">State / Region</label>
                        <input type="text" id="state" name="state" value="<?= htmlspecialchars($store->state ?? $store['state'] ?? '') ?>"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="e.g. Greater Accra">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Country</label>
                        <select id="country" name="country"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                            <option value="Ghana" <?= (($store->country ?? $store['country'] ?? 'Ghana') === 'Ghana') ? 'selected' : '' ?>>Ghana</option>
                            <option value="Nigeria" <?= (($store->country ?? '') === 'Nigeria') ? 'selected' : '' ?>>Nigeria</option>
                            <option value="Kenya" <?= (($store->country ?? '') === 'Kenya') ? 'selected' : '' ?>>Kenya</option>
                            <option value="South Africa" <?= (($store->country ?? '') === 'South Africa') ? 'selected' : '' ?>>South Africa</option>
                            <option value="Other" <?= (($store->country ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Business Information (Optional)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tax ID / GRA Number</label>
                        <input type="text" id="tax_id" name="tax_id" value="<?= htmlspecialchars($store->tax_id ?? $store['tax_id'] ?? '') ?>"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="e.g. GRA-XXXX-XXXX">
                    </div>
                    <div>
                        <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Registration Number</label>
                        <input type="text" id="registration_number" name="registration_number" value="<?= htmlspecialchars($store->registration_number ?? $store['registration_number'] ?? '') ?>"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                               placeholder="e.g. CS-XXXXX-XXXX">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-primary-700 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
