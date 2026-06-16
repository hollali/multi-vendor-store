<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6">
        <a href="/" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-800 dark:text-gray-200 font-medium">Shop</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Filters Sidebar -->
        <aside id="filter-sidebar" class="lg:w-72 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-24">
                <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2"><i class="fas fa-filter text-primary-600"></i> Filters</h3>
                    <button type="button" id="filter-toggle" class="lg:hidden text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fas fa-times"></i></button>
                </div>

                <form action="/shop" method="GET" id="filter-form" class="divide-y divide-gray-100 dark:divide-gray-700">
                    <!-- Categories -->
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Categories</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto scrollbar-hide">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                    $catSlug = $cat->slug ?? $cat['slug'] ?? '';
                                    $catName = $cat->name ?? $cat['name'] ?? '';
                                    $checked = in_array($catSlug, $selectedCategories ?? []) ? 'checked' : '';
                                    ?>
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="checkbox" name="category[]" value="<?= htmlspecialchars($catSlug) ?>" <?= $checked ?> class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-700 focus:ring-primary-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition"><?= htmlspecialchars($catName) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-400">No categories available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Price Range</h4>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">GH₵</span>
                                <input type="number" name="min_price" placeholder="Min" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" min="0" class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                            </div>
                            <span class="text-gray-400 text-xs">—</span>
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">GH₵</span>
                                <input type="number" name="max_price" placeholder="Max" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" min="0" class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                            </div>
                        </div>
                    </div>

                    <!-- Brands -->
                    <?php if (!empty($brands)): ?>
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Brands</h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto scrollbar-hide">
                            <?php foreach ($brands as $brand): ?>
                                <?php
                                $brandSlug = $brand->slug ?? $brand['slug'] ?? '';
                                $brandName = $brand->name ?? $brand['name'] ?? '';
                                $checked = in_array($brandSlug, $selectedBrands ?? []) ? 'checked' : '';
                                ?>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" name="brand[]" value="<?= htmlspecialchars($brandSlug) ?>" <?= $checked ?> class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-700 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition"><?= htmlspecialchars($brandName) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="p-4 space-y-2">
                        <button type="submit" class="w-full py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">Apply Filters</button>
                        <a href="/shop" class="block w-full py-2.5 text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg transition">Clear All</a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Sort & Count -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-800 dark:text-gray-200"><?= $total ?? 0 ?></span> products found
                </p>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="button" id="filter-open-btn" class="lg:hidden flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1 sm:flex-none justify-center">
                        <i class="fas fa-sliders-h"></i> Filters
                    </button>
                    <select name="sort" onchange="window.location.href=this.value" class="flex-1 sm:flex-none px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 cursor-pointer">
                        <?php
                        $currentSort = $_GET['sort'] ?? 'newest';
                        $baseUrl = '/shop?' . http_build_query(array_merge($_GET, ['sort' => '__VAL__']));
                        $sortOptions = [
                            'newest' => 'Newest',
                            'price_asc' => 'Price: Low to High',
                            'price_desc' => 'Price: High to Low',
                            'name_asc' => 'Name: A-Z',
                            'name_desc' => 'Name: Z-A',
                            'rating' => 'Top Rated',
                        ];
                        ?>
                        <?php foreach ($sortOptions as $val => $label): ?>
                            <option value="<?= str_replace('__VAL__', $val, $baseUrl) ?>" <?= $currentSort === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <?php if (!empty($products) && count($products) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                    <?php foreach ($products as $product): ?>
                        <?php include __DIR__ . '/_product_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if (!empty($pagination) && $pagination['lastPage'] > 1): ?>
                    <div class="flex items-center justify-center gap-1.5 mt-8">
                        <?php
                        $currentPage = $pagination['currentPage'] ?? 1;
                        $lastPage = $pagination['lastPage'] ?? 1;
                        $urlTemplate = $pagination['urlTemplate'] ?? '/shop?page=__PAGE__';
                        ?>
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= str_replace('__PAGE__', $currentPage - 1, $urlTemplate) ?>" class="px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <?php
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                        if ($start > 1): ?>
                            <a href="<?= str_replace('__PAGE__', '1', $urlTemplate) ?>" class="px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition">1</a>
                            <?php if ($start > 2): ?><span class="px-2 text-gray-400">...</span><?php endif; ?>
                        <?php endif; ?>
                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <a href="<?= str_replace('__PAGE__', $i, $urlTemplate) ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition <?= $i === $currentPage ? 'bg-primary-700 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($end < $lastPage): ?>
                            <?php if ($end < $lastPage - 1): ?><span class="px-2 text-gray-400">...</span><?php endif; ?>
                            <a href="<?= str_replace('__PAGE__', $lastPage, $urlTemplate) ?>" class="px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition"><?= $lastPage ?></a>
                        <?php endif; ?>
                        <?php if ($currentPage < $lastPage): ?>
                            <a href="<?= str_replace('__PAGE__', $currentPage + 1, $urlTemplate) ?>" class="px-3 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                        <i class="fas fa-search text-3xl text-gray-300 dark:text-gray-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">No products found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Try adjusting your filters or search terms.</p>
                    <a href="/shop" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">Clear Filters <i class="fas fa-times"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Mobile Filter Overlay -->
<div id="filter-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('filter-sidebar');
    var overlay = document.getElementById('filter-overlay');
    var openBtn = document.getElementById('filter-open-btn');
    var closeBtn = document.getElementById('filter-toggle');

    if (sidebar && openBtn && closeBtn && overlay) {
        function openFilters() {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('fixed', 'inset-y-0', 'left-0', 'z-50', 'w-80', 'max-w-[85vw]', 'overflow-y-auto');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeFilters() {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50', 'w-80', 'max-w-[85vw]', 'overflow-y-auto');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        openBtn.addEventListener('click', openFilters);
        closeBtn.addEventListener('click', closeFilters);
        overlay.addEventListener('click', closeFilters);
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
