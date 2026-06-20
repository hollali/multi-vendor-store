<?php require __DIR__ . '/../layouts/header.php'; ?>

<div id="home-content" class="w-full">
<!-- Hero Carousel -->
<section class="relative bg-gray-100 dark:bg-gray-900 overflow-hidden">
    <div id="hero-carousel" class="relative w-full h-[50vw] min-h-[280px] max-h-[600px]">
        <?php if (!empty($banners) && is_array($banners)): ?>
            <?php foreach ($banners as $index => $banner): ?>
                <div class="hero-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?= $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>">
                    <img src="<?= htmlspecialchars($banner['image'] ?? '') ?>" alt="<?= htmlspecialchars($banner['title'] ?? 'Banner') ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
                    <div class="absolute inset-0 flex items-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="max-w-xl">
                                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-3"><?= htmlspecialchars($banner['title'] ?? '') ?></h2>
                                <?php if (!empty($banner['subtitle'])): ?>
                                    <p class="text-sm sm:text-lg text-gray-200 mb-5"><?= htmlspecialchars($banner['subtitle']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($banner['link'])): ?>
                                    <a href="<?= htmlspecialchars($banner['link']) ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all text-sm sm:text-base">
                                        Shop Now
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- Dots -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                <?php foreach ($banners as $index => $banner): ?>
                    <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full transition-all <?= $index === 0 ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/80' ?>" data-slide="<?= $index ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
            <!-- Arrows -->
            <button type="button" id="hero-prev" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur flex items-center justify-center text-white transition hidden sm:flex" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <button type="button" id="hero-next" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur flex items-center justify-center text-white transition hidden sm:flex" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-800 to-primary-600">
                <div class="text-center text-white px-4">
                    <i class="fas fa-bolt text-6xl mb-4 opacity-50"></i>
                    <h2 class="text-3xl sm:text-5xl font-extrabold mb-3">Welcome to The Middle Man</h2>
                    <p class="text-lg text-gray-200 mb-5">Ghana's trusted multi-vendor marketplace</p>
                    <a href="/shop" class="inline-flex items-center gap-2 px-6 py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg transition"><i class="fas fa-store"></i> Start Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Categories Section -->
<section class="py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Shop by Category</h2>
            <a href="/shop" class="text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <?php if (!empty($categories) && count($categories) > 0): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $catSlug = $cat->slug ?? $cat['slug'] ?? '';
                    $catName = $cat->name ?? $cat['name'] ?? '';
                    $catIcon = $cat->icon ?? $cat['icon'] ?? 'fa-tag';
                    $catImage = $cat->image ?? $cat['image'] ?? '';
                    ?>
                    <a href="/shop/category/<?= htmlspecialchars($catSlug) ?>" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="aspect-[4/3] relative">
                            <?php if ($catImage): ?>
                                <img src="<?= htmlspecialchars($catImage) ?>" alt="<?= htmlspecialchars($catName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/10">
                                    <i class="fas <?= htmlspecialchars($catIcon) ?> text-4xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 text-center">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition"><?= htmlspecialchars($catName) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                <?php $fallbackCategories = [
                    ['slug' => 'electronics', 'name' => 'Electronics', 'icon' => 'fa-tv'],
                    ['slug' => 'fashion', 'name' => 'Fashion', 'icon' => 'fa-tshirt'],
                    ['slug' => 'home', 'name' => 'Home & Kitchen', 'icon' => 'fa-couch'],
                    ['slug' => 'phones', 'name' => 'Phones & Tablets', 'icon' => 'fa-mobile-alt'],
                ]; ?>
                <?php foreach ($fallbackCategories as $fc): ?>
                    <a href="/shop/category/<?= $fc['slug'] ?>" class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="aspect-[4/3] flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/10">
                            <i class="fas <?= $fc['icon'] ?> text-4xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="p-3 text-center">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200"><?= $fc['name'] ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-8 sm:py-12 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Featured Products</h2>
            <a href="/shop?filter=featured" class="text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <?php if (!empty($featuredProducts) && count($featuredProducts) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <?php foreach ($featuredProducts as $product): ?>
                    <?php include __DIR__ . '/_product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-star text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">No featured products at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Latest Products Section -->
<section class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">New Arrivals</h2>
            <a href="/shop?sort=newest" class="text-sm font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <?php if (!empty($latestProducts) && count($latestProducts) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <?php foreach ($latestProducts as $product): ?>
                    <?php include __DIR__ . '/_product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">No new arrivals yet. Check back soon!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-12 sm:py-16 bg-gradient-to-r from-primary-800 to-primary-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <i class="fas fa-envelope-open-text text-4xl text-white/60 mb-4"></i>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-2">Stay in the Loop</h2>
            <p class="text-primary-100 mb-6 text-sm sm:text-base">Get the latest deals, new arrivals, and exclusive offers delivered to your inbox.</p>
            <form action="/newsletter/subscribe" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <input type="email" name="email" required placeholder="Enter your email address" class="flex-1 px-4 py-3 rounded-lg text-sm bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                <button type="submit" class="px-6 py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl text-sm whitespace-nowrap">Subscribe <i class="fas fa-paper-plane ml-1"></i></button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-dot');
    var prevBtn = document.getElementById('hero-prev');
    var nextBtn = document.getElementById('hero-next');
    if (!slides.length) return;
    var current = 0;
    var total = slides.length;
    var interval;

    function goTo(index) {
        slides.forEach(function(s, i) {
            s.classList.toggle('opacity-100', i === index);
            s.classList.toggle('opacity-0', i !== index);
            s.classList.toggle('z-10', i === index);
            s.classList.toggle('z-0', i !== index);
        });
        dots.forEach(function(d, i) {
            d.classList.toggle('bg-white', i === index);
            d.classList.toggle('w-6', i === index);
            d.classList.toggle('bg-white/50', i !== index);
            d.classList.remove('w-6');
            if (i !== index) d.classList.add('w-2.5');
        });
        current = index;
    }

    function next() { goTo((current + 1) % total); }
    function prev() { goTo((current - 1 + total) % total); }

    function resetInterval() {
        clearInterval(interval);
        interval = setInterval(next, 5000);
    }

    if (nextBtn) nextBtn.addEventListener('click', function() { next(); resetInterval(); });
    if (prevBtn) prevBtn.addEventListener('click', function() { prev(); resetInterval(); });
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            goTo(parseInt(this.dataset.slide));
            resetInterval();
        });
    });

    interval = setInterval(next, 5000);
});
</script>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
