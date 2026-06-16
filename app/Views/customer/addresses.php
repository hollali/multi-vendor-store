<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'addresses'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">My Addresses</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your shipping and billing addresses</p>
            </div>
            <button onclick="openAddressModal()" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus"></i> Add New Address
            </button>
        </div>

        <?php $addresses = $addresses ?? []; ?>
        <?php if (empty($addresses)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-orange-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No addresses saved</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add an address to make checkout faster.</p>
                <button onclick="openAddressModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-plus"></i> Add New Address</button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                <?php foreach ($addresses as $addr): ?>
                    <?php $isDefault = $addr->is_default ?? $addr['is_default'] ?? false; ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 relative">
                        <?php if ($isDefault): ?>
                            <span class="absolute top-3 right-3 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"><i class="fas fa-check-circle mr-1"></i> Default</span>
                        <?php endif; ?>
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-700 dark:text-primary-400 flex-shrink-0"><i class="fas fa-map-marker-alt"></i></span>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm"><?= htmlspecialchars($addr->label ?? $addr['label'] ?? 'Address') ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?= htmlspecialchars($addr->full_name ?? $addr['full_name'] ?? '') ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($addr->street_address ?? $addr['street_address'] ?? '') ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($addr->city ?? $addr['city'] ?? '') ?>, <?= htmlspecialchars($addr->state ?? $addr['state'] ?? '') ?> <?= htmlspecialchars($addr->postal_code ?? $addr['postal_code'] ?? '') ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($addr->country ?? $addr['country'] ?? 'Ghana') ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Phone: <?= htmlspecialchars($addr->phone ?? $addr['phone'] ?? '') ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <button onclick="openAddressModal(<?= htmlspecialchars(json_encode($addr)) ?>)" class="text-sm text-primary-700 dark:text-primary-400 hover:underline font-medium"><i class="fas fa-edit mr-1"></i> Edit</button>
                            <form action="/addresses/delete" method="POST" onsubmit="return confirm('Delete this address?')" class="inline">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($addr->id ?? $addr['id'] ?? '') ?>">
                                <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium ml-2"><i class="fas fa-trash-alt mr-1"></i> Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="address-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeAddressModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="address-modal-title">Add New Address</h3>
                <button onclick="closeAddressModal()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fas fa-times"></i></button>
            </div>
            <form id="address-form" method="POST" action="/addresses/save" class="p-6 space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <input type="hidden" name="id" id="address-id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                    <input type="text" name="label" id="address-label" placeholder="e.g. Home, Office" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                        <input type="text" name="full_name" id="address-full_name" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <input type="tel" name="phone" id="address-phone" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Street Address</label>
                    <input type="text" name="street_address" id="address-street_address" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                        <input type="text" name="city" id="address-city" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State</label>
                        <input type="text" name="state" id="address-state" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" id="address-postal_code" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                    <select name="country" id="address-country" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                        <option value="Ghana">Ghana</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Kenya">Kenya</option>
                        <option value="South Africa">South Africa</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="address-is_default" value="1" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-700 focus:ring-primary-500">
                    <label for="address-is_default" class="text-sm text-gray-700 dark:text-gray-300">Set as default address</label>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeAddressModal()" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-700 hover:bg-primary-800 rounded-lg transition shadow-sm">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddressModal(data) {
    document.getElementById('address-modal').classList.remove('hidden');
    document.getElementById('address-modal-title').textContent = data ? 'Edit Address' : 'Add New Address';
    document.getElementById('address-id').value = data?.id ?? '';
    document.getElementById('address-label').value = data?.label ?? '';
    document.getElementById('address-full_name').value = data?.full_name ?? '';
    document.getElementById('address-phone').value = data?.phone ?? '';
    document.getElementById('address-street_address').value = data?.street_address ?? '';
    document.getElementById('address-city').value = data?.city ?? '';
    document.getElementById('address-state').value = data?.state ?? '';
    document.getElementById('address-postal_code').value = data?.postal_code ?? '';
    document.getElementById('address-country').value = data?.country ?? 'Ghana';
    document.getElementById('address-is_default').checked = data?.is_default ?? false;
    document.body.style.overflow = 'hidden';
}
function closeAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAddressModal();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
