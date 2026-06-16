<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'products'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">My Products</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your product catalog.</p>
            </div>
            <a href="/vendor/products/create" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus-circle"></i> Add New Product
            </a>
        </div>

        <div class="flex flex-wrap gap-2 mb-5">
            <?php
            $currentFilter = $_GET['status'] ?? 'all';
            $filters = [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'active', 'label' => 'Active'],
                ['key' => 'draft', 'label' => 'Draft'],
                ['key' => 'pending', 'label' => 'Pending'],
                ['key' => 'rejected', 'label' => 'Rejected'],
            ];
            ?>
            <?php foreach ($filters as $f): ?>
                <a href="?status=<?= $f['key'] ?>"
                   class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition <?= $currentFilter === $f['key'] ? 'bg-primary-700 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' ?>">
                    <?= $f['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php $products = $products ?? []; ?>
        <?php if (empty($products)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-boxes text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No products yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Start selling by adding your first product.</p>
                <a href="/vendor/products/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-plus-circle"></i> Add Your First Product
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Product</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">SKU</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Price</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Stock</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($products as $product): ?>
                                <?php
                                $status = $product->status ?? $product['status'] ?? 'draft';
                                $statusStyles = ['active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', 'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300', 'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'];
                                $image = $product->image ?? $product['image'] ?? $product->thumbnail ?? $product['thumbnail'] ?? '';
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                                <?php if ($image): ?>
                                                    <img src="<?= htmlspecialchars($image) ?>" alt="" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-400 text-sm"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-white truncate max-w-[200px]"><?= htmlspecialchars($product->name ?? $product['name'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars($product->sku ?? $product['sku'] ?? '') ?></td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">GH₵ <?= number_format($product->price ?? $product['price'] ?? 0, 2) ?></td>
                                    <td class="px-5 py-4">
                                        <?php $stock = $product->stock ?? $product['stock'] ?? 0; ?>
                                        <span class="<?= $stock > 10 ? 'text-green-600 dark:text-green-400' : ($stock > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') ?> font-medium"><?= $stock ?></span>
                                    </td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusStyles[$status] ?? $statusStyles['draft'] ?>"><?= ucfirst($status) ?></span></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="/vendor/products/<?= htmlspecialchars($product->id ?? $product['id'] ?? '') ?>/edit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition"><i class="fas fa-edit"></i> Edit</a>
                                            <form action="/vendor/products/<?= htmlspecialchars($product->id ?? $product['id'] ?? '') ?>/delete" method="POST" onsubmit="return confirm('Delete this product?')" class="inline">
                                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Page <?= $currentPage ?? 1 ?> of <?= $totalPages ?></p>
                    <div class="flex gap-2">
                        <?php if (($currentPage ?? 1) > 1): ?>
                            <a href="?page=<?= ($currentPage ?? 1) - 1 ?>&status=<?= $currentFilter ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&status=<?= $currentFilter ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($currentPage ?? 1) == $i ? 'bg-primary-700 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if (($currentPage ?? 1) < $totalPages): ?>
                            <a href="?page=<?= ($currentPage ?? 1) + 1 ?>&status=<?= $currentFilter ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
