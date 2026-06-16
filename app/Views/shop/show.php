<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$p = $product ?? (object)[];
$pId = $p->id ?? $p['id'] ?? 0;
$pName = $p->name ?? $p['name'] ?? '';
$pSlug = $p->slug ?? $p['slug'] ?? '';
$pPrice = (float)($p->price ?? $p['price'] ?? 0);
$pSalePrice = (float)($p->sale_price ?? $p['sale_price'] ?? 0);
$pDescription = $p->description ?? $p['description'] ?? '';
$pShortDesc = $p->short_description ?? $p['short_description'] ?? $pDescription;
$pSku = $p->sku ?? $p['sku'] ?? '';
$pRating = (float)($p->rating ?? $p['rating'] ?? 0);
$pReviewsCount = (int)($p->reviews_count ?? $p['reviews_count'] ?? $p->reviews ?? $p['reviews'] ?? 0);
$pImages = $p->images ?? $p['images'] ?? [];
if (is_string($pImages)) $pImages = json_decode($pImages, true) ?? [];
$pMainImage = $p->image ?? $p['image'] ?? ($pImages[0] ?? '');
$pSpecs = $p->specifications ?? $p['specifications'] ?? $p->specs ?? $p['specs'] ?? [];
if (is_string($pSpecs)) $pSpecs = json_decode($pSpecs, true) ?? [];
$pVariants = $p->variants ?? $p['variants'] ?? [];
if (is_string($pVariants)) $pVariants = json_decode($pVariants, true) ?? [];
$pCategory = $p->category ?? $p['category'] ?? (object)[];
$pCategoryName = is_object($pCategory) ? ($pCategory->name ?? '') : ($pCategory['name'] ?? '');
$pCategorySlug = is_object($pCategory) ? ($pCategory->slug ?? '') : ($pCategory['slug'] ?? '');
$pBrand = $p->brand ?? $p['brand'] ?? (object)[];
$pBrandName = is_object($pBrand) ? ($pBrand->name ?? '') : ($pBrand['name'] ?? '');
$pStore = $p->store ?? $p['store'] ?? (object)[];
$pStoreName = is_object($pStore) ? ($pStore->name ?? '') : ($pStore['name'] ?? '');
$pStoreSlug = is_object($pStore) ? ($pStore->slug ?? '') : ($pStore['slug'] ?? '');
$pStoreRating = is_object($pStore) ? ($pStore->rating ?? $pStore->avg_rating ?? 0) : ($pStore['rating'] ?? $pStore['avg_rating'] ?? 0);
$pStoreProductCount = is_object($pStore) ? ($pStore->products_count ?? $pStore->product_count ?? 0) : ($pStore['products_count'] ?? $pStore['product_count'] ?? 0);
$hasDiscount = $pSalePrice > 0 && $pSalePrice < $pPrice;
$displayPrice = $hasDiscount ? $pSalePrice : $pPrice;
$discountPercent = $hasDiscount ? round((1 - $pSalePrice / $pPrice) * 100) : 0;
$currencySymbol = 'GH₵';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 flex-wrap">
        <a href="/" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <?php if ($pCategoryName): ?>
            <a href="/shop/category/<?= htmlspecialchars($pCategorySlug) ?>" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><?= htmlspecialchars($pCategoryName) ?></a>
            <i class="fas fa-chevron-right text-[10px]"></i>
        <?php else: ?>
            <a href="/shop" class="hover:text-primary-700 dark:hover:text-primary-400 transition">Shop</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
        <?php endif; ?>
        <span class="text-gray-800 dark:text-gray-200 font-medium truncate"><?= htmlspecialchars($pName) ?></span>
    </nav>

    <!-- Product Detail -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10 mb-8">
        <!-- Left: Gallery -->
        <div class="space-y-3">
            <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 group" id="main-image-container">
                <?php if ($pMainImage): ?>
                    <img id="main-product-image" src="<?= htmlspecialchars($pMainImage) ?>" alt="<?= htmlspecialchars($pName) ?>" class="w-full h-full object-cover cursor-crosshair">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                <?php endif; ?>
                <?php if ($hasDiscount): ?>
                    <span class="absolute top-3 left-3 px-3 py-1.5 bg-red-500 text-white text-sm font-bold rounded-lg shadow-md">-<?= $discountPercent ?>%</span>
                <?php endif; ?>
            </div>
            <?php if (count($pImages) > 1): ?>
                <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                    <?php foreach ($pImages as $idx => $img): ?>
                        <button type="button" class="thumbnail-btn flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden border-2 transition <?= $idx === 0 ? 'border-primary-600' : 'border-gray-200 dark:border-gray-700 hover:border-primary-300' ?>" data-image="<?= htmlspecialchars($img) ?>">
                            <img src="<?= htmlspecialchars($img) ?>" alt="" class="w-full h-full object-cover">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Product Info -->
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3"><?= htmlspecialchars($pName) ?></h1>

            <!-- Rating -->
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center text-yellow-400 text-sm">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i <= round($pRating) ? '' : '-o text-gray-300 dark:text-gray-600' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">(<?= $pReviewsCount ?> review<?= $pReviewsCount !== 1 ? 's' : '' ?>)</span>
            </div>

            <!-- Price -->
            <div class="flex items-baseline gap-3 mb-4">
                <span class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white"><?= $currencySymbol ?><?= number_format($displayPrice, 2) ?></span>
                <?php if ($hasDiscount): ?>
                    <span class="text-lg text-gray-400 line-through"><?= $currencySymbol ?><?= number_format($pPrice, 2) ?></span>
                    <span class="px-2.5 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg">Save <?= $discountPercent ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Short Description -->
            <?php if ($pShortDesc): ?>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-5 leading-relaxed"><?= htmlspecialchars($pShortDesc) ?></p>
            <?php endif; ?>

            <!-- Variants -->
            <?php if (!empty($pVariants)): ?>
                <?php
                $groupedVariants = [];
                foreach ($pVariants as $v) {
                    $type = $v['type'] ?? $v->type ?? 'option';
                    $groupedVariants[$type][] = $v;
                }
                ?>
                <?php foreach ($groupedVariants as $type => $options): ?>
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 capitalize"><?= htmlspecialchars($type) ?>:</h4>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($options as $opt): ?>
                                <?php
                                $optValue = $opt['value'] ?? $opt->value ?? '';
                                $optLabel = $opt['label'] ?? $opt->label ?? $optValue;
                                $optStock = (int)($opt['stock'] ?? $opt->stock ?? 10);
                                $disabled = $optStock <= 0;
                                ?>
                                <button type="button"
                                    class="variant-btn px-4 py-2 text-sm rounded-lg border transition font-medium
                                        <?= $disabled ? 'border-gray-100 dark:border-gray-700 text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-primary-500 hover:text-primary-700 dark:hover:text-primary-400' ?>"
                                    data-type="<?= htmlspecialchars($type) ?>"
                                    data-value="<?= htmlspecialchars($optValue) ?>"
                                    <?= $disabled ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($optLabel) ?>
                                    <?php if ($disabled): ?><span class="text-[10px] block text-red-400">Out of stock</span><?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Quantity + Add to Cart -->
            <div class="flex items-stretch gap-3 mb-6">
                <div class="flex items-center border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                    <button type="button" id="qty-decrease" class="px-3 py-2.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition" aria-label="Decrease quantity"><i class="fas fa-minus text-xs"></i></button>
                    <input type="number" id="quantity-input" value="1" min="1" max="99" class="w-12 text-center text-sm font-medium bg-transparent border-x border-gray-200 dark:border-gray-600 py-2.5 focus:outline-none dark:text-gray-200 [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" id="qty-increase" class="px-3 py-2.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition" aria-label="Increase quantity"><i class="fas fa-plus text-xs"></i></button>
                </div>
                <button type="button" id="add-to-cart-btn" data-product-id="<?= $pId ?>" class="flex-1 py-3 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button type="button" id="wishlist-btn" data-product-id="<?= $pId ?>" class="px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-400 hover:text-red-500 hover:border-red-300 dark:hover:border-red-700 transition" aria-label="Add to wishlist">
                    <i class="far fa-heart text-lg"></i>
                </button>
            </div>

            <!-- Product Meta -->
            <div class="space-y-2 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
                <?php if ($pSku): ?>
                    <p><span class="font-medium text-gray-700 dark:text-gray-300">SKU:</span> <?= htmlspecialchars($pSku) ?></p>
                <?php endif; ?>
                <?php if ($pCategoryName): ?>
                    <p><span class="font-medium text-gray-700 dark:text-gray-300">Category:</span> <a href="/shop/category/<?= htmlspecialchars($pCategorySlug) ?>" class="text-primary-700 dark:text-primary-400 hover:underline"><?= htmlspecialchars($pCategoryName) ?></a></p>
                <?php endif; ?>
                <?php if ($pBrandName): ?>
                    <p><span class="font-medium text-gray-700 dark:text-gray-300">Brand:</span> <?= htmlspecialchars($pBrandName) ?></p>
                <?php endif; ?>
                <?php if ($pStoreName): ?>
                    <p><span class="font-medium text-gray-700 dark:text-gray-300">Sold by:</span> <a href="/shop/store/<?= htmlspecialchars($pStoreSlug) ?>" class="text-primary-700 dark:text-primary-400 hover:underline"><?= htmlspecialchars($pStoreName) ?></a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto scrollbar-hide">
            <button type="button" class="tab-btn px-5 sm:px-6 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition text-primary-700 dark:text-primary-400 border-primary-700 dark:border-primary-400" data-tab="description">Description</button>
            <?php if (!empty($pSpecs)): ?>
                <button type="button" class="tab-btn px-5 sm:px-6 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition" data-tab="specs">Specifications</button>
            <?php endif; ?>
            <button type="button" class="tab-btn px-5 sm:px-6 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition" data-tab="reviews">Reviews (<?= $pReviewsCount ?>)</button>
            <?php if ($canReview ?? false): ?>
                <button type="button" class="tab-btn px-5 sm:px-6 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition" data-tab="write-review">Write a Review</button>
            <?php endif; ?>
        </div>

        <!-- Description Tab -->
        <div class="tab-content p-5 sm:p-6" id="tab-description">
            <?php if ($pDescription): ?>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed">
                    <?= $pDescription ?>
                </div>
            <?php else: ?>
                <p class="text-gray-400 text-sm">No description available.</p>
            <?php endif; ?>
        </div>

        <!-- Specifications Tab -->
        <?php if (!empty($pSpecs)): ?>
            <div class="tab-content hidden p-5 sm:p-6" id="tab-specs">
                <table class="w-full text-sm">
                    <tbody>
                        <?php foreach ($pSpecs as $key => $val): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <td class="py-3 pr-4 font-medium text-gray-700 dark:text-gray-300 w-1/3"><?= htmlspecialchars(is_string($key) ? $key : ($val['label'] ?? $val['name'] ?? $key)) ?></td>
                                <td class="py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars(is_string($val) ? $val : ($val['value'] ?? $val['description'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Reviews Tab -->
        <div class="tab-content hidden p-5 sm:p-6" id="tab-reviews">
            <?php if (!empty($reviews) && count($reviews) > 0): ?>
                <div class="space-y-5 divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($reviews as $review): ?>
                        <?php
                        $rRating = (int)($review->rating ?? $review['rating'] ?? 5);
                        $rName = $review->user_name ?? $review['user_name'] ?? ($review->user->name ?? $review['user']['name'] ?? 'Anonymous');
                        $rDate = $review->created_at ?? $review['created_at'] ?? '';
                        $rComment = $review->comment ?? $review['comment'] ?? $review->body ?? $review['body'] ?? '';
                        ?>
                        <div class="pt-5 first:pt-0">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <span class="font-semibold text-sm text-gray-800 dark:text-gray-200"><?= htmlspecialchars($rName) ?></span>
                                    <div class="flex items-center text-yellow-400 text-xs mt-0.5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $rRating ? '' : '-o text-gray-300 dark:text-gray-600' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php if ($rDate): ?>
                                    <span class="text-xs text-gray-400"><?= date('M d, Y', strtotime((string)$rDate)) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($rComment): ?>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"><?= htmlspecialchars($rComment) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($reviewsPagination) && $reviewsPagination['lastPage'] > 1): ?>
                    <div class="flex items-center justify-center gap-1.5 mt-6">
                        <?php $rp = $reviewsPagination; ?>
                        <?php for ($i = 1; $i <= $rp['lastPage']; $i++): ?>
                            <a href="?page=<?= $i ?>#tab-reviews" class="px-3 py-1.5 rounded text-xs font-medium transition <?= ($rp['currentPage'] ?? 1) === $i ? 'bg-primary-700 text-white' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-10">
                    <i class="far fa-comment-dots text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No reviews yet. Be the first to review this product!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Write Review Tab -->
        <?php if ($canReview ?? false): ?>
            <div class="tab-content hidden p-5 sm:p-6" id="tab-write-review">
                <form action="/product/<?= $pId ?>/review" method="POST" class="max-w-lg">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Rating</label>
                        <div class="flex items-center gap-1 text-2xl text-gray-300 dark:text-gray-600" id="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="far fa-star cursor-pointer hover:text-yellow-400 star" data-value="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="rating-input" value="5">
                    </div>
                    <div class="mb-4">
                        <label for="review-comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Review</label>
                        <textarea id="review-comment" name="comment" rows="4" required placeholder="Share your experience with this product..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 placeholder-gray-400"></textarea>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">Submit Review</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Store Info Card -->
    <?php if ($pStoreName): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sm:p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/20 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-store text-xl text-primary-600 dark:text-primary-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($pStoreName) ?></h3>
                    <?php if ($pStoreRating > 0): ?>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <div class="flex items-center text-yellow-400 text-xs">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= round($pStoreRating) ? '' : '-o text-gray-300 dark:text-gray-600' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?= number_format($pStoreRating, 1) ?></span>
                        </div>
                    <?php endif; ?>
                    <p class="text-xs text-gray-400"><?= $pStoreProductCount ?> product<?= $pStoreProductCount !== 1 ? 's' : '' ?></p>
                </div>
                <a href="/shop/store/<?= htmlspecialchars($pStoreSlug) ?>" class="px-4 py-2 text-sm font-medium text-primary-700 dark:text-primary-400 border border-primary-300 dark:border-primary-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition whitespace-nowrap">Visit Store <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts) && count($relatedProducts) > 0): ?>
        <section>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-4">Related Products</h2>
            <div class="flex gap-4 overflow-x-auto scrollbar-hide pb-2">
                <?php foreach ($relatedProducts as $product): ?>
                    <div class="flex-shrink-0 w-48 sm:w-56">
                        <?php include __DIR__ . '/_product_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail switching
    document.querySelectorAll('.thumbnail-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var img = this.dataset.image;
            document.getElementById('main-product-image').src = img;
            document.querySelectorAll('.thumbnail-btn').forEach(function(b) {
                b.classList.remove('border-primary-600');
                b.classList.add('border-gray-200', 'dark:border-gray-700');
            });
            this.classList.remove('border-gray-200', 'dark:border-gray-700');
            this.classList.add('border-primary-600');
        });
    });

    // Quantity
    var qtyInput = document.getElementById('quantity-input');
    var decBtn = document.getElementById('qty-decrease');
    var incBtn = document.getElementById('qty-increase');
    if (qtyInput && decBtn && incBtn) {
        decBtn.addEventListener('click', function() {
            var val = parseInt(qtyInput.value) || 1;
            if (val > 1) qtyInput.value = val - 1;
        });
        incBtn.addEventListener('click', function() {
            var val = parseInt(qtyInput.value) || 1;
            if (val < 99) qtyInput.value = val + 1;
        });
    }

    // Variants
    document.querySelectorAll('.variant-btn:not([disabled])').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var type = this.dataset.type;
            document.querySelectorAll('.variant-btn[data-type="' + type + '"]').forEach(function(b) {
                b.classList.remove('border-primary-500', 'text-primary-700', 'dark:text-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20');
                b.classList.add('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
            });
            this.classList.remove('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('border-primary-500', 'text-primary-700', 'dark:text-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20');
        });
    });

    // Tabs
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tabId = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(function(b) {
                b.classList.remove('text-primary-700', 'dark:text-primary-400', 'border-primary-700', 'dark:border-primary-400');
                b.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            });
            this.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            this.classList.add('text-primary-700', 'dark:text-primary-400', 'border-primary-700', 'dark:border-primary-400');
            document.querySelectorAll('.tab-content').forEach(function(c) {
                c.classList.add('hidden');
            });
            var target = document.getElementById('tab-' + tabId);
            if (target) target.classList.remove('hidden');
        });
    });

    // Star rating
    var stars = document.querySelectorAll('#star-rating .star');
    var ratingInput = document.getElementById('rating-input');
    if (stars.length && ratingInput) {
        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                var val = parseInt(this.dataset.value);
                ratingInput.value = val;
                stars.forEach(function(s, i) {
                    if (i < val) { s.classList.remove('far'); s.classList.add('fas', 'text-yellow-400'); }
                    else { s.classList.remove('fas', 'text-yellow-400'); s.classList.add('far'); }
                });
            });
            star.addEventListener('mouseenter', function() {
                var val = parseInt(this.dataset.value);
                stars.forEach(function(s, i) {
                    if (i < val) { s.classList.remove('far'); s.classList.add('fas', 'text-yellow-400'); }
                });
            });
            star.addEventListener('mouseleave', function() {
                var selected = parseInt(ratingInput.value) || 0;
                stars.forEach(function(s, i) {
                    if (i < selected) { s.classList.remove('far'); s.classList.add('fas', 'text-yellow-400'); }
                    else { s.classList.remove('fas', 'text-yellow-400'); s.classList.add('far'); }
                });
            });
        });
    }

    // Add to cart
    document.getElementById('add-to-cart-btn')?.addEventListener('click', function() {
        var productId = this.dataset.productId;
        var qty = parseInt(document.getElementById('quantity-input')?.value || 1);
        fetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId + '&quantity=' + qty + '&_csrf_token=<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>'
        }).then(function(r) { return r.json(); }).then(function(data) {
            var badge = document.getElementById('cart-badge');
            if (badge) { badge.textContent = data.cart_count || 0; badge.classList.remove('hidden'); }
            var flash = document.createElement('div');
            flash.className = 'toast flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg bg-green-50 dark:bg-green-900/30 border-green-400 dark:border-green-600 text-green-800 dark:text-green-300 fixed top-20 right-4 z-[100] max-w-sm';
            flash.innerHTML = '<i class="fas fa-check-circle flex-shrink-0"></i><p class="text-sm font-medium flex-1">Added to cart!</p><button onclick="this.parentElement.remove()" class="flex-shrink-0 opacity-60 hover:opacity-100"><i class="fas fa-times text-sm"></i></button>';
            document.body.appendChild(flash);
            setTimeout(function() { flash.style.animation = 'slideOut 0.3s ease-out forwards'; setTimeout(function() { flash.remove(); }, 300); }, 3000);
        }).catch(function() {});
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
