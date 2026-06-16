<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'categories'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 lg:p-8 xl:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Categories</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organize your product categories.</p>
            </div>
            <button onclick="document.getElementById('categoryForm').scrollIntoView({behavior: 'smooth'})" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus-circle"></i> Add Category
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div id="categoryForm" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= isset($editCategory) ? 'Edit Category' : 'New Category' ?></h2>
                    <form action="/admin/categories/save" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                        <?php if (isset($editCategory)): ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($editCategory->id ?? $editCategory['id'] ?? '') ?>">
                        <?php endif; ?>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name</label>
                                <input type="text" name="name" required value="<?= htmlspecialchars($editCategory->name ?? $editCategory['name'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="e.g. Electronics">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                                <input type="text" name="slug" value="<?= htmlspecialchars($editCategory->slug ?? $editCategory['slug'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="e.g. electronics">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Parent Category</label>
                                <select name="parent_id" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                                    <option value="">— None (Top Level) —</option>
                                    <?php foreach ($allCategories ?? $categories ?? [] as $cat): ?>
                                        <?php $catId = $cat->id ?? $cat['id'] ?? 0; ?>
                                        <option value="<?= $catId ?>" <?= (isset($editCategory) && (($editCategory->parent_id ?? $editCategory['parent_id'] ?? '') == $catId)) ? 'selected' : '' ?>><?= htmlspecialchars($cat->name ?? $cat['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="Brief description..."><?= htmlspecialchars($editCategory->description ?? $editCategory['description'] ?? '') ?></textarea>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                    <i class="fas fa-save mr-1.5"></i> <?= isset($editCategory) ? 'Update' : 'Save' ?>
                                </button>
                                <?php if (isset($editCategory)): ?>
                                    <a href="/admin/categories" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Category Tree</h2>
                    </div>
                    <?php $categories = $categories ?? []; ?>
                    <?php if (empty($categories)): ?>
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-tags text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No categories yet</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Create your first category using the form.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Name</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Slug</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Parent</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Products</th>
                                        <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($categories as $cat): ?>
                                        <?php
                                        $catId = $cat->id ?? $cat['id'] ?? 0;
                                        $children = $cat->children ?? $cat['children'] ?? [];
                                        $hasChildren = !empty($children);
                                        ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                                                    <?php if ($hasChildren): ?><i class="fas fa-folder-open text-yellow-500 text-xs"></i><?php else: ?><i class="fas fa-tag text-primary-500 text-xs"></i><?php endif; ?>
                                                    <?= htmlspecialchars($cat->name ?? $cat['name'] ?? '') ?>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars($cat->slug ?? $cat['slug'] ?? '') ?></td>
                                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($cat->parent_name ?? $cat['parent_name'] ?? '—') ?></td>
                                            <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= number_format($cat->products_count ?? $cat['products_count'] ?? 0) ?></td>
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <a href="/admin/categories?edit=<?= $catId ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition"><i class="fas fa-edit"></i> Edit</a>
                                                    <button onclick="openDeleteModal(<?= $catId ?>, '<?= htmlspecialchars(addslashes($cat->name ?? $cat['name'] ?? '')) ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-trash"></i> Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if ($hasChildren): ?>
                                            <?php foreach ($children as $child): ?>
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition bg-gray-50/50 dark:bg-gray-800/50">
                                                    <td class="px-5 py-4 pl-10">
                                                        <div class="flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                                                            <i class="fas fa-level-indent text-gray-300 text-xs"></i>
                                                            <i class="fas fa-tag text-primary-400 text-xs"></i>
                                                            <?= htmlspecialchars($child->name ?? $child['name'] ?? '') ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars($child->slug ?? $child['slug'] ?? '') ?></td>
                                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($cat->name ?? $cat['name'] ?? '') ?></td>
                                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= number_format($child->products_count ?? $child['products_count'] ?? 0) ?></td>
                                                    <td class="px-5 py-4">
                                                        <div class="flex items-center gap-2">
                                                            <a href="/admin/categories?edit=<?= $child->id ?? $child['id'] ?? 0 ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition"><i class="fas fa-edit"></i> Edit</a>
                                                            <button onclick="openDeleteModal(<?= $child->id ?? $child['id'] ?? 0 ?>, '<?= htmlspecialchars(addslashes($child->name ?? $child['name'] ?? '')) ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-trash"></i> Delete</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Category</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Are you sure you want to delete <strong id="deleteCategoryName" class="text-gray-900 dark:text-white"></strong>?</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-6">This action cannot be undone. Products may become uncategorized.</p>
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
    document.getElementById('deleteCategoryName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/categories/' + id + '/delete';
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