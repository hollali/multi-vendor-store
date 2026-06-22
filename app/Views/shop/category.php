<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 flex-wrap">
        <a href="<?= $url('/') ?>" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <a href="<?= $url('/shop') ?>" class="hover:text-primary-700 dark:hover:text-primary-400 transition">Shop</a>
        <?php if (!empty($parentCategory)): ?>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <a href="<?= $url('/shop/category/' . htmlspecialchars($parentCategory->slug ?? $parentCategory['slug'] ?? '')) ?>" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><?= htmlspecialchars($parentCategory->name ?? $parentCategory['name'] ?? '') ?></a>
        <?php endif; ?>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-800 dark:text-gray-200 font-medium"><?= htmlspecialchars($category->name ?? $category['name'] ?? '') ?></span>
    </nav>

    <!-- Category Header -->
    <?php $cat = $category ?? []; ?>
    <?php $catName = $cat->name ?? $cat['name'] ?? 'Category'; ?>
    <?php $catDesc = $cat->description ?? $cat['description'] ?? ''; ?>
    <?php $catImage = $cat->image ?? $cat['image'] ?? ''; ?>
    <?php $catBanner = $cat->banner ?? $cat['banner'] ?? $catImage; ?>

    <div class="relative rounded-xl overflow-hidden mb-6 bg-primary-700">
        <?php if ($catBanner): ?>
            <img src="<?= htmlspecialchars($catBanner) ?>" alt="<?= htmlspecialchars($catName) ?>" class="w-full h-40 sm:h-56 object-cover opacity-40">
        <?php endif; ?>
        <div class="absolute inset-0 flex items-center px-6 sm:px-10">
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-2"><?= htmlspecialchars($catName) ?></h1>
                <?php if ($catDesc): ?>
                    <p class="text-sm sm:text-base text-primary-100 max-w-2xl"><?= htmlspecialchars($catDesc) ?></p>
                <?php endif; ?>
                <?php if (!empty($total)): ?>
                    <p class="text-sm text-primary-200 mt-2"><span class="font-semibold"><?= $total ?></span> products available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Subcategories -->
    <?php if (!empty($subcategories) && count($subcategories) > 0): ?>
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide pb-2">
                <a href="<?= $url('/shop/category/' . htmlspecialchars($cat->slug ?? $cat['slug'] ?? '')) ?>" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap border <?= empty($_GET['subcategory']) ? 'bg-primary-700 text-white border-primary-700 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600' ?>">All</a>
                <?php foreach ($subcategories as $sub): ?>
                    <?php
                    $subSlug = $sub->slug ?? $sub['slug'] ?? '';
                    $subName = $sub->name ?? $sub['name'] ?? '';
                    $subActive = ($_GET['subcategory'] ?? '') === $subSlug;
                    ?>
                    <a href="<?= $url('/shop/category/' . htmlspecialchars($cat->slug ?? $cat['slug'] ?? '') . '?subcategory=' . htmlspecialchars($subSlug)) ?>"
                       class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap border <?= $subActive ? 'bg-primary-700 text-white border-primary-700 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600' ?>">
                        <?= htmlspecialchars($subName) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Products -->
    <?php if (!empty($products) && count($products) > 0): ?>
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-medium text-gray-800 dark:text-gray-200"><?= $total ?? count($products) ?></span> products</p>
            <select onchange="window.location.href=this.value" class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 cursor-pointer">
                <?php
                $currentSort = $_GET['sort'] ?? 'newest';
                $baseUrl = $url('/shop/category/' . htmlspecialchars($cat->slug ?? $cat['slug'] ?? '') . '?' . http_build_query(array_merge($_GET, ['sort' => '__VAL__'])));
                $sortOptions = ['newest' => 'Newest', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'name_asc' => 'Name: A-Z', 'rating' => 'Top Rated'];
                ?>
                <?php foreach ($sortOptions as $val => $label): ?>
                    <option value="<?= str_replace('__VAL__', $val, $baseUrl) ?>" <?= $currentSort === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <?php foreach ($products as $product): ?>
                <?php include __DIR__ . '/_product_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($pagination) && $pagination['lastPage'] > 1): ?>
            <div class="flex items-center justify-center gap-1.5 mt-8">
                <?php
                $currentPage = $pagination['currentPage'] ?? 1;
                $lastPage = $pagination['lastPage'] ?? 1;
                $urlTemplate = $pagination['urlTemplate'] ?? '/shop/category/' . htmlspecialchars($cat->slug ?? $cat['slug'] ?? '') . '?page=__PAGE__';
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
                <i class="fas fa-box-open text-3xl text-gray-300 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">No products in this category</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Check back later or browse other categories.</p>
            <a href="<?= $url('/shop') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-store"></i> Browse All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
