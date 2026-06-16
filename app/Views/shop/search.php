<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6">
        <a href="/" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-800 dark:text-gray-200 font-medium">Search</span>
    </nav>

    <!-- Search Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-1 w-full">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    Search results for "<?= htmlspecialchars($query ?? $_GET['q'] ?? '') ?>"
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= $total ?? 0 ?> product<?= ($total ?? 0) !== 1 ? 's' : '' ?> found
                </p>
            </div>
            <form action="/shop/search" method="GET" class="w-full sm:w-auto sm:min-w-[300px]">
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($query ?? $_GET['q'] ?? '') ?>" placeholder="Search again..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 placeholder-gray-400">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (!empty($products) && count($products) > 0): ?>
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Showing <span class="font-medium"><?= $from ?? 1 ?></span>–<span class="font-medium"><?= $to ?? count($products) ?></span> of <span class="font-medium"><?= $total ?? count($products) ?></span> results</p>
            <select onchange="window.location.href=this.value" class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 cursor-pointer">
                <?php
                $currentSort = $_GET['sort'] ?? 'relevance';
                $baseUrl = '/shop/search?' . http_build_query(array_merge($_GET, ['sort' => '__VAL__']));
                $sortOptions = ['relevance' => 'Relevance', 'newest' => 'Newest', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low'];
                ?>
                <?php foreach ($sortOptions as $val => $label): ?>
                    <option value="<?= str_replace('__VAL__', $val, $baseUrl) ?>" <?= $currentSort === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <?php foreach ($products as $product): ?>
                <?php include __DIR__ . '/_product_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($pagination) && $pagination['lastPage'] > 1): ?>
            <div class="flex items-center justify-center gap-1.5 mt-8">
                <?php
                $currentPage = $pagination['currentPage'] ?? 1;
                $lastPage = $pagination['lastPage'] ?? 1;
                $urlTemplate = $pagination['urlTemplate'] ?? '/shop/search?q=' . urlencode($query ?? $_GET['q'] ?? '') . '&page=__PAGE__';
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
        <!-- No Results -->
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 dark:bg-gray-700 mb-5">
                <i class="fas fa-search-minus text-4xl text-gray-300 dark:text-gray-500"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">No results found</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                We couldn't find any products matching "<strong><?= htmlspecialchars($query ?? $_GET['q'] ?? '') ?></strong>". Try adjusting your search terms.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-2 mb-6">
                <span class="text-xs text-gray-400">Suggestions:</span>
                <a href="/shop/category/electronics" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-400 transition">Electronics</a>
                <a href="/shop/category/fashion" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-400 transition">Fashion</a>
                <a href="/shop/category/home" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-400 transition">Home & Kitchen</a>
                <a href="/shop/category/phones" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-400 transition">Phones & Tablets</a>
            </div>
            <form action="/shop/search" method="GET" class="max-w-md mx-auto">
                <div class="flex gap-2">
                    <input type="text" name="q" placeholder="Try a different search..." class="flex-1 px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 placeholder-gray-400">
                    <button type="submit" class="px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
