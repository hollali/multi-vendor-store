<?php require __DIR__ . '/../layouts/header.php'; ?>

<div id="home-content" class="w-full">

<!-- Hero Carousel -->
<section class="relative bg-gray-100 dark:bg-gray-900 overflow-hidden">
    <?php if (!empty($banners) && is_array($banners)): ?>
        <div class="hero-swiper swiper w-full h-[280px] sm:h-[400px] lg:h-[500px]">
            <div class="swiper-wrapper">
                <?php foreach ($banners as $banner): ?>
                    <div class="swiper-slide relative overflow-hidden bg-primary-800">
                        <?php if (!empty($banner['image'])): ?>
                            <img src="<?= htmlspecialchars($banner['image']) ?>" alt="<?= htmlspecialchars($banner['title'] ?? 'Banner') ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-bolt text-8xl text-white/20"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black/60"></div>
                        <div class="absolute inset-0 flex items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="max-w-lg lg:max-w-xl">
                                <?php if (!empty($banner['title'])): ?>
                                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-2 lg:mb-4 drop-shadow-lg"><?= htmlspecialchars($banner['title']) ?></h2>
                                <?php endif; ?>
                                <?php if (!empty($banner['subtitle'])): ?>
                                    <p class="text-sm sm:text-base lg:text-lg text-gray-200 mb-4 lg:mb-6 max-w-md drop-shadow"><?= htmlspecialchars($banner['subtitle']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($banner['link'])): ?>
                                    <a href="<?= htmlspecialchars($banner['link']) ?>" class="inline-flex items-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:shadow-accent-600/30 transition-all text-sm sm:text-base">
                                        Shop Now <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination !bottom-3 sm:!bottom-5"></div>
            <div class="swiper-button-next !text-white !hidden sm:!flex !w-10 !h-10 !rounded-full !bg-white/20 hover:!bg-white/30 !backdrop-blur-sm !transition-all after:!text-base"></div>
            <div class="swiper-button-prev !text-white !hidden sm:!flex !w-10 !h-10 !rounded-full !bg-white/20 hover:!bg-white/30 !backdrop-blur-sm !transition-all after:!text-base"></div>
        </div>
    <?php else: ?>
        <div class="relative bg-primary-800 h-[280px] sm:h-[400px] lg:h-[500px] flex items-center">
            <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-2">Welcome to The Middle Man</h2>
                <p class="text-sm sm:text-base lg:text-lg text-primary-100 mb-6"><?= htmlspecialchars($countryName ?? 'Ghana') ?>'s trusted multi-vendor marketplace</p>
                <a href="<?= $url('/shop') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-store"></i> Start Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Features Strip -->
<section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-truck text-primary-700 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Free Delivery</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Orders over <?= $geo_currency_symbol ?? 'GH₵' ?>200</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-primary-700 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Secure Payment</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">100% protected</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-rotate-left text-primary-700 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Easy Returns</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">30-day return policy</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-headset text-primary-700 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">24/7 Support</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Dedicated help</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Shop by Category</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Explore our wide range of categories</p>
            </div>
            <a href="<?= $url('/shop') ?>" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-primary-700 dark:text-primary-400 border border-primary-300 dark:border-primary-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition whitespace-nowrap">View All <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
        <?php if (!empty($categories) && count($categories) > 0): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $catSlug = $cat->slug ?? $cat['slug'] ?? '';
                    $catName = $cat->name ?? $cat['name'] ?? '';
                    $catIcon = $cat->icon ?? $cat['icon'] ?? 'fa-tag';
                    $catImage = $cat->image ?? $cat['image'] ?? '';
                    $catCount = $cat->products_count ?? $cat['products_count'] ?? 0;
                    ?>
                    <a href="<?= $url('/shop/category/' . $catSlug) ?>" class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="aspect-square relative">
                            <?php if ($catImage): ?>
                                <img src="<?= htmlspecialchars($catImage) ?>" alt="<?= htmlspecialchars($catName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-primary-50 dark:bg-primary-900/30">
                                    <i class="fas <?= htmlspecialchars($catIcon) ?> text-3xl sm:text-4xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-2.5 text-center">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition block truncate"><?= htmlspecialchars($catName) ?></span>
                            <?php if ($catCount > 0): ?>
                                <span class="text-[11px] text-gray-400"><?= $catCount ?> items</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                <?php $fallbackCategories = [
                    ['slug' => 'electronics', 'name' => 'Electronics', 'icon' => 'fa-tv'],
                    ['slug' => 'fashion', 'name' => 'Fashion', 'icon' => 'fa-tshirt'],
                    ['slug' => 'home', 'name' => 'Home & Kitchen', 'icon' => 'fa-couch'],
                    ['slug' => 'phones', 'name' => 'Phones & Tablets', 'icon' => 'fa-mobile-alt'],
                    ['slug' => 'beauty', 'name' => 'Beauty', 'icon' => 'fa-spa'],
                    ['slug' => 'sports', 'name' => 'Sports', 'icon' => 'fa-running'],
                ]; ?>
                <?php foreach ($fallbackCategories as $fc): ?>
                    <a href="<?= $url('/shop/category/' . $fc['slug']) ?>" class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="aspect-square flex items-center justify-center bg-primary-50 dark:bg-primary-900/30">
                            <i class="fas <?= $fc['icon'] ?> text-4xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="p-2.5 text-center">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200"><?= $fc['name'] ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="text-center mt-6 sm:hidden">
            <a href="<?= $url('/shop') ?>" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium text-primary-700 dark:text-primary-400 border border-primary-300 dark:border-primary-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">View All Categories <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<?php if (!empty($featuredProducts) && count($featuredProducts) > 0): ?>
<section class="py-10 sm:py-14 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Featured Products</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Handpicked just for you</p>
            </div>
            <a href="<?= $url('/shop?filter=featured') ?>" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <?php foreach ($featuredProducts as $product): ?>
                <?php include __DIR__ . '/_product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Trending Products -->
<?php if (!empty($trendingProducts) && count($trendingProducts) > 0): ?>
<section class="py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Trending Now</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Most popular products in <?= htmlspecialchars($countryName ?? 'your area') ?></p>
            </div>
            <a href="<?= $url('/shop?sort=rating') ?>" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <div class="trending-swiper swiper overflow-hidden -mx-1 px-1">
            <div class="swiper-wrapper">
                <?php foreach ($trendingProducts as $product): ?>
                    <div class="swiper-slide h-auto">
                        <?php include __DIR__ . '/_product_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next !text-primary-700 dark:!text-primary-400 !w-9 !h-9 !rounded-full !bg-white dark:!bg-gray-800 !shadow-lg hover:!shadow-xl after:!text-sm !transition-all !-right-1 sm:!-right-3"></div>
            <div class="swiper-button-prev !text-primary-700 dark:!text-primary-400 !w-9 !h-9 !rounded-full !bg-white dark:!bg-gray-800 !shadow-lg hover:!shadow-xl after:!text-sm !transition-all !-left-1 sm:!-left-3"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Top Stores -->
<?php if (!empty($topStores) && count($topStores) > 0): ?>
<section class="py-10 sm:py-14 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Top Stores</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Shop from <?= htmlspecialchars($countryName ?? 'your area') ?>'s best sellers</p>
            </div>
            <a href="<?= $url('/shop') ?>" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <?php foreach ($topStores as $store): ?>
                <?php
                $sName = $store->store_name ?? $store['store_name'] ?? $store->name ?? $store['name'] ?? '';
                $sSlug = $store->store_slug ?? $store['store_slug'] ?? $store->slug ?? $store['slug'] ?? '';
                $sLogo = $store->logo ?? $store['logo'] ?? '';
                $sProductCount = $store->product_count ?? $store['product_count'] ?? $store->products_count ?? $store['products_count'] ?? 0;
                $sRating = $store->avg_rating ?? $store['avg_rating'] ?? $store->rating ?? $store['rating'] ?? 0;
                ?>
                <a href="<?= $url('/shop/store/' . $sSlug) ?>" class="group bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <?php if ($sLogo): ?>
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-white border border-gray-200 dark:border-gray-600 flex-shrink-0">
                                <img src="<?= htmlspecialchars($sLogo) ?>" alt="<?= htmlspecialchars($sName) ?>" class="w-full h-full object-cover">
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-store text-primary-600 dark:text-primary-400 text-lg"></i>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate group-hover:text-primary-700 dark:group-hover:text-primary-400 transition"><?= htmlspecialchars($sName) ?></h3>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span><i class="fas fa-box mr-0.5"></i><?= $sProductCount ?> products</span>
                            </div>
                        </div>
                    </div>
                    <?php if ($sRating > 0): ?>
                        <div class="flex items-center gap-1.5">
                            <div class="flex items-center text-yellow-400 text-xs">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= round($sRating) ? '' : '-o text-gray-300 dark:text-gray-600' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400"><?= number_format($sRating, 1) ?></span>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Section -->
<section class="py-12 sm:py-16 bg-primary-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-white/10 mb-5">
                <i class="fas fa-envelope-open-text text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-2">Stay in the Loop</h2>
            <p class="text-primary-100 mb-6 text-sm sm:text-base">Get the latest deals, new arrivals, and exclusive offers delivered to your inbox.</p>
            <form action="<?= $url('/newsletter/subscribe') ?>" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                <?= $csrf_field() ?>
                <input type="email" name="email" required placeholder="Enter your email address" class="flex-1 px-4 py-3 rounded-lg text-sm bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                <button type="submit" class="px-6 py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl text-sm whitespace-nowrap">Subscribe <i class="fas fa-paper-plane ml-1"></i></button>
            </form>
        </div>
    </div>
</section>

<!-- Location-based messaging -->
<?php if (!empty($countryName)): ?>
<section class="py-6 bg-gray-100 dark:bg-gray-900 border-y border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-map-marker-alt text-primary-700 dark:text-primary-400 mr-1"></i>
            You're shopping in <strong class="text-gray-800 dark:text-gray-200"><?= htmlspecialchars($countryName) ?></strong> — Prices shown in <?= $geo_currency_code ?? 'GHS' ?> (<?= $geo_currency_symbol ?? 'GH₵' ?>)
        </p>
    </div>
</section>
<?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
