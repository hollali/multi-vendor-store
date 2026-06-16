<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'wishlist'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">My Wishlist</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= $wishlistCount ?? count($wishlist ?? []) ?> item(s)</p>
            </div>
        </div>

        <?php $wishlist = $wishlist ?? []; ?>
        <?php if (empty($wishlist)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center">
                    <i class="far fa-heart text-pink-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Your wishlist is empty</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Save your favorite items here to shop later.</p>
                <a href="/shop" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-store"></i> Browse Shop</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <?php foreach ($wishlist as $item): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden group hover:shadow-md transition relative">
                        <div class="relative aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <img src="<?= htmlspecialchars($item->image ?? $item['image'] ?? '/assets/img/placeholder.png') ?>" alt="<?= htmlspecialchars($item->name ?? $item['name'] ?? 'Product') ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <form action="/wishlist/remove" method="POST" class="absolute top-2 right-2">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($item->id ?? $item['id'] ?? $item->product_id ?? $item['product_id'] ?? '') ?>">
                                <button type="submit" class="w-8 h-8 rounded-full bg-white/90 dark:bg-gray-800/90 flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 shadow-sm transition" title="Remove"><i class="fas fa-times text-sm"></i></button>
                            </form>
                        </div>
                        <div class="p-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($item->name ?? $item['name'] ?? '') ?></p>
                            <p class="text-sm font-bold text-primary-700 dark:text-primary-400 mt-1">GHS <?= number_format($item->price ?? $item['price'] ?? 0, 2) ?></p>
                            <div class="mt-3">
                                <form action="/cart/add" method="POST">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($item->id ?? $item['id'] ?? $item->product_id ?? $item['product_id'] ?? '') ?>">
                                    <button type="submit" class="w-full text-center px-4 py-2 bg-primary-700 hover:bg-primary-800 text-white text-xs font-medium rounded-lg transition shadow-sm"><i class="fas fa-shopping-cart mr-1"></i> Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
