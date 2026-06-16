<?php
$p = $product;
$pId = $p->id ?? $p['id'] ?? 0;
$pSlug = $p->slug ?? $p['slug'] ?? '';
$pName = $p->name ?? $p['name'] ?? '';
$pImage = $p->image ?? $p['images'][0] ?? $p['image_url'] ?? '';
$pPrice = (float)($p->price ?? $p['price'] ?? 0);
$pSalePrice = (float)($p->sale_price ?? $p['sale_price'] ?? 0);
$pRating = (float)($p->rating ?? $p['rating'] ?? 0);
$pReviews = (int)($p->reviews_count ?? $p['reviews_count'] ?? $p->reviews ?? $p['reviews'] ?? 0);
$pCurrency = $p->currency ?? $p['currency'] ?? 'GHS';
$hasDiscount = $pSalePrice > 0 && $pSalePrice < $pPrice;
$displayPrice = $hasDiscount ? $pSalePrice : $pPrice;
$discountPercent = $hasDiscount ? round((1 - $pSalePrice / $pPrice) * 100) : 0;
$currencySymbol = $pCurrency === 'GHS' ? 'GH₵' : ($pCurrency === 'USD' ? '$' : $pCurrency . ' ');
?>
<div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
    <a href="/shop/<?= htmlspecialchars($pSlug) ?>" class="relative block aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
        <?php if ($pImage): ?>
            <img src="<?= htmlspecialchars($pImage) ?>" alt="<?= htmlspecialchars($pName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-image text-4xl text-gray-300 dark:text-gray-600"></i>
            </div>
        <?php endif; ?>
        <?php if ($hasDiscount): ?>
            <span class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-md shadow-sm">-<?= $discountPercent ?>%</span>
        <?php endif; ?>
        <button type="button" data-product-id="<?= $pId ?>" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-red-500 transition shadow-sm wishlist-btn" aria-label="Add to wishlist"><i class="far fa-heart"></i></button>
        <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
            <span class="text-white text-xs font-medium"><i class="fas fa-eye mr-1"></i> Quick View</span>
        </div>
    </a>
    <div class="p-3 sm:p-4 flex flex-col flex-1">
        <a href="/shop/<?= htmlspecialchars($pSlug) ?>" class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-primary-700 dark:hover:text-primary-400 transition line-clamp-2 mb-1.5"><?= htmlspecialchars($pName) ?></a>
        <?php if ($pRating > 0): ?>
            <div class="flex items-center gap-1.5 mb-1.5">
                <div class="flex items-center text-yellow-400 text-xs">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i <= round($pRating) ? '' : '-o text-gray-300 dark:text-gray-600' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-[11px] text-gray-400">(<?= $pReviews ?>)</span>
            </div>
        <?php endif; ?>
        <div class="flex items-center gap-2 mb-3 mt-auto">
            <span class="text-base sm:text-lg font-bold text-gray-900 dark:text-white"><?= $currencySymbol ?><?= number_format($displayPrice, 2) ?></span>
            <?php if ($hasDiscount): ?>
                <span class="text-sm text-gray-400 line-through"><?= $currencySymbol ?><?= number_format($pPrice, 2) ?></span>
            <?php endif; ?>
        </div>
        <button type="button" data-product-id="<?= $pId ?>" class="w-full py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 add-to-cart-btn">
            <i class="fas fa-shopping-cart text-xs"></i> Add to Cart
        </button>
    </div>
</div>
