<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'brands'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Brands</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage product brands.</p>
            </div>
            <button onclick="document.getElementById('brandForm').scrollIntoView({behavior: 'smooth'})" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus-circle"></i> Add Brand
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div id="brandForm" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= isset($editBrand) ? 'Edit Brand' : 'New Brand' ?></h2>
                    <form action="/admin/brands/save" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                        <?php if (isset($editBrand)): ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($editBrand->id ?? $editBrand['id'] ?? '') ?>">
                        <?php endif; ?>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand Name</label>
                                <input type="text" name="name" required value="<?= htmlspecialchars($editBrand->name ?? $editBrand['name'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="e.g. Apple">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                                <input type="text" name="slug" value="<?= htmlspecialchars($editBrand->slug ?? $editBrand['slug'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="e.g. apple">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="Brief description..."><?= htmlspecialchars($editBrand->description ?? $editBrand['description'] ?? '') ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden flex-shrink-0 border-2 border-dashed border-gray-300 dark:border-gray-600">
                                        <?php $logo = $editBrand->logo ?? $editBrand['logo'] ?? ''; ?>
                                        <?php if ($logo): ?>
                                            <img src="<?= htmlspecialchars($logo) ?>" alt="" class="w-full h-full object-contain">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-400 text-xl"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="logo" accept="image/*" class="flex-1 text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 dark:file:bg-primary-900/20 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/40">
                                </div>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                    <i class="fas fa-save mr-1.5"></i> <?= isset($editBrand) ? 'Update' : 'Save' ?>
                                </button>
                                <?php if (isset($editBrand)): ?>
                                    <a href="/admin/brands" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <?php $brands = $brands ?? []; ?>
                    <?php if (empty($brands)): ?>
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-copyright text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No brands yet</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Create your first brand using the form.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Logo</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Name</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Products</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($brands as $brand): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-5 py-4">
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
                                                    <?php $logo = $brand->logo ?? $brand['logo'] ?? ''; ?>
                                                    <?php if ($logo): ?>
                                                        <img src="<?= htmlspecialchars($logo) ?>" alt="" class="w-full h-full object-contain">
                                                    <?php else: ?>
                                                        <i class="fas fa-copyright text-gray-400"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($brand->name ?? $brand['name'] ?? '') ?></span>
                                            </td>
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= number_format($brand->products_count ?? $brand['products_count'] ?? 0) ?></td>
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <a href="/admin/brands?edit=<?= $brand->id ?? $brand['id'] ?? 0 ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition"><i class="fas fa-edit"></i> Edit</a>
                                                    <button onclick="openDeleteModal(<?= $brand->id ?? $brand['id'] ?? 0 ?>, '<?= htmlspecialchars(addslashes($brand->name ?? $brand['name'] ?? '')) ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-trash"></i> Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (isset($totalPages) && $totalPages > 1): ?>
                            <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 dark:border-gray-700">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Brand</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Are you sure you want to delete <strong id="deleteBrandName" class="text-gray-900 dark:text-white"></strong>?</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-6">This action cannot be undone.</p>
                <form id="deleteForm" method="POST" class="flex gap-3 justify-center">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-trash mr-1.5"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteBrandName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/brands/' + id + '/delete';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function() {
            slugInput.value = nameInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        });
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>