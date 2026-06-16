<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$store = $store ?? (object)[];
$storeName = $store->name ?? $store['name'] ?? 'Store';
$storeSlug = $store->slug ?? $store['slug'] ?? '';
$storeDesc = $store->description ?? $store['description'] ?? '';
$storeLogo = $store->logo ?? $store['logo'] ?? '';
$storeBanner = $store->banner ?? $store['banner'] ?? $storeLogo;
$storeRating = (float)($store->rating ?? $store['rating'] ?? $store->avg_rating ?? $store['avg_rating'] ?? 0);
$storeJoined = $store->created_at ?? $store['created_at'] ?? '';
$storeProductCount = (int)($store->products_count ?? $store['products_count'] ?? $store->product_count ?? $store['product_count'] ?? 0);
$storeReviewsCount = (int)($store->reviews_count ?? $store['reviews_count'] ?? 0);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 flex-wrap">
        <a href="/" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <a href="/shop" class="hover:text-primary-700 dark:hover:text-primary-400 transition">Stores</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-800 dark:text-gray-200 font-medium"><?= htmlspecialchars($storeName) ?></span>
    </nav>

    <!-- Store Banner -->
    <div class="relative rounded-xl overflow-hidden mb-6 bg-gradient-to-r from-gray-800 to-gray-900">
        <?php if ($storeBanner): ?>
            <img src="<?= htmlspecialchars($storeBanner) ?>" alt="<?= htmlspecialchars($storeName) ?>" class="w-full h-40 sm:h-56 object-cover opacity-50">
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
        <div class="absolute inset-0 flex items-center px-6 sm:px-10">
            <div class="flex items-center gap-4 sm:gap-6">
                <?php if ($storeLogo): ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 border-white/30 shadow-lg flex-shrink-0 bg-white">
                        <img src="<?= htmlspecialchars($storeLogo) ?>" alt="" class="w-full h-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg flex-shrink-0">
                        <i class="fas fa-store text-3xl text-white"></i>
                    </div>
                <?php endif; ?>
                <div class="text-white">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold"><?= htmlspecialchars($storeName) ?></h1>
                    <?php if ($storeDesc): ?>
                        <p class="text-sm text-gray-200 max-w-xl mt-1 line-clamp-2"><?= htmlspecialchars($storeDesc) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $storeProductCount ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Products</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="flex items-center justify-center gap-1 text-2xl font-bold text-gray-900 dark:text-white">
                <span><?= number_format($storeRating, 1) ?></span>
                <i class="fas fa-star text-yellow-400 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rating (<?= $storeReviewsCount ?>)</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $storeReviewsCount ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Reviews</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <?php if ($storeJoined): ?>
                <p class="text-lg font-bold text-gray-900 dark:text-white"><?= date('M Y', strtotime((string)$storeJoined)) ?></p>
            <?php else: ?>
                <p class="text-lg font-bold text-gray-900 dark:text-white">—</p>
            <?php endif; ?>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Joined</p>
        </div>
    </div>

    <!-- Products -->
    <?php if (!empty($products) && count($products) > 0): ?>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Products from <?= htmlspecialchars($storeName) ?></h2>
            <select onchange="window.location.href=this.value" class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 cursor-pointer">
                <?php
                $currentSort = $_GET['sort'] ?? 'newest';
                $baseUrl = '/shop/store/' . htmlspecialchars($storeSlug) . '?' . http_build_query(array_merge($_GET, ['sort' => '__VAL__']));
                $sortOptions = ['newest' => 'Newest', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'name_asc' => 'Name: A-Z', 'rating' => 'Top Rated'];
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
                $urlTemplate = $pagination['urlTemplate'] ?? '/shop/store/' . htmlspecialchars($storeSlug) . '?page=__PAGE__';
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
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">No products yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">This store hasn't listed any products yet.</p>
            <a href="/shop" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-store"></i> Browse Other Stores</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
