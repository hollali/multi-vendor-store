<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$cartItems = $cartItems ?? $items ?? $cart ?? [];
$cartTotal = (float)($cartTotal ?? $subtotal ?? 0);
$couponDiscount = (float)($couponDiscount ?? 0);
$taxRate = (float)($taxRate ?? 0.10);
$shipping = (float)($shipping ?? 0);
$subtotal = (float)($subtotal ?? $cartTotal);
$discount = $couponDiscount;
$tax = (float)($tax ?? $subtotal * $taxRate);
$finalTotal = $subtotal - $discount + $tax + $shipping;
$currencySymbol = 'GH₵';
$hasItems = !empty($cartItems) && (is_array($cartItems) || count($cartItems) > 0);
$itemCount = is_array($cartItems) ? count($cartItems) : (count($cartItems) ?? 0);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6">
        <a href="/" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-800 dark:text-gray-200 font-medium">Cart</span>
    </nav>

    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-6">Shopping Cart <?php if ($itemCount > 0): ?><span class="text-base font-normal text-gray-500 dark:text-gray-400">(<?= $itemCount ?> item<?= $itemCount !== 1 ? 's' : '' ?>)</span><?php endif; ?></h1>

    <?php if ($hasItems): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($cartItems as $item): ?>
                    <?php
                    $ciId = $item->id ?? $item['id'] ?? 0;
                    $ciProductId = $item->product_id ?? $item['product_id'] ?? 0;
                    $ciName = $item->name ?? $item['name'] ?? ($item->product->name ?? $item['product']['name'] ?? 'Product');
                    $ciSlug = $item->slug ?? $item['slug'] ?? ($item->product->slug ?? $item['product']['slug'] ?? '');
                    $ciImage = $item->image ?? $item['image'] ?? ($item->product->image ?? $item['product']['image'] ?? '');
                    $ciPrice = (float)($item->price ?? $item['price'] ?? 0);
                    $ciQuantity = (int)($item->quantity ?? $item['quantity'] ?? 1);
                    $ciVariant = $item->variant ?? $item['variant'] ?? '';
                    $ciTotal = $ciPrice * $ciQuantity;
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 cart-item">
                        <div class="flex gap-4">
                            <a href="/shop/<?= htmlspecialchars($ciSlug) ?>" class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                <?php if ($ciImage): ?>
                                    <img src="<?= htmlspecialchars($ciImage) ?>" alt="<?= htmlspecialchars($ciName) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-300 dark:text-gray-600"></i></div>
                                <?php endif; ?>
                            </a>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <a href="/shop/<?= htmlspecialchars($ciSlug) ?>" class="text-sm sm:text-base font-medium text-gray-800 dark:text-gray-200 hover:text-primary-700 dark:hover:text-primary-400 transition line-clamp-2"><?= htmlspecialchars($ciName) ?></a>
                                    <button type="button" class="remove-item-btn flex-shrink-0 p-1 text-gray-400 hover:text-red-500 transition" data-item-id="<?= $ciId ?>" aria-label="Remove"><i class="fas fa-trash-alt text-sm"></i></button>
                                </div>
                                <?php if ($ciVariant): ?>
                                    <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($ciVariant) ?></p>
                                <?php endif; ?>
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                        <button type="button" class="qty-dec px-2.5 py-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-xs" data-item-id="<?= $ciId ?>"><i class="fas fa-minus"></i></button>
                                        <input type="number" value="<?= $ciQuantity ?>" min="1" max="99" class="qty-input w-10 text-center text-sm font-medium bg-transparent border-x border-gray-200 dark:border-gray-600 py-1.5 focus:outline-none dark:text-gray-200 [&::-webkit-inner-spin-button]:appearance-none" data-item-id="<?= $ciId ?>">
                                        <button type="button" class="qty-inc px-2.5 py-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-xs" data-item-id="<?= $ciId ?>"><i class="fas fa-plus"></i></button>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= $currencySymbol ?><?= number_format($ciTotal, 2) ?></p>
                                        <p class="text-xs text-gray-400"><?= $currencySymbol ?><?= number_format($ciPrice, 2) ?> each</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Coupon -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" id="coupon-input" placeholder="Enter coupon code" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 placeholder-gray-400" value="<?= htmlspecialchars($_GET['coupon'] ?? $appliedCoupon ?? '') ?>">
                        </div>
                        <button type="button" id="apply-coupon-btn" class="px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm whitespace-nowrap">Apply</button>
                    </div>
                    <?php if (!empty($couponMessage)): ?>
                        <p class="text-xs mt-2 <?= $couponSuccess ? 'text-green-600 dark:text-green-400' : 'text-red-500' ?>"><i class="fas <?= $couponSuccess ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-1"></i><?= htmlspecialchars($couponMessage) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sticky top-24">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span><?= $currencySymbol ?><?= number_format($subtotal, 2) ?></span>
                        </div>
                        <?php if ($discount > 0): ?>
                            <div class="flex items-center justify-between text-green-600 dark:text-green-400">
                                <span>Discount</span>
                                <span>-<?= $currencySymbol ?><?= number_format($discount, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Tax</span>
                            <span><?= $currencySymbol ?><?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span><?= $shipping > 0 ? $currencySymbol . number_format($shipping, 2) : 'Free' ?></span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-base font-bold text-gray-900 dark:text-white">
                            <span>Total</span>
                            <span><?= $currencySymbol ?><?= number_format($finalTotal, 2) ?></span>
                        </div>
                    </div>
                    <a href="/checkout" class="mt-4 w-full py-3 bg-primary-700 hover:bg-primary-800 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-lock text-xs"></i> Proceed to Checkout
                    </a>
                    <div class="flex items-center justify-center gap-3 mt-3 text-xs text-gray-400">
                        <span><i class="fas fa-lock mr-1"></i> Secure</span>
                        <span><i class="fas fa-shield-alt mr-1"></i> SSL</span>
                        <span><i class="fas fa-credit-card mr-1"></i> Paystack</span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty Cart -->
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-gray-100 dark:bg-gray-700 mb-5">
                <i class="fas fa-shopping-bag text-5xl text-gray-300 dark:text-gray-500"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Your Cart is Empty</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">Looks like you haven't added anything yet. Browse our categories and discover amazing deals!</p>
            <a href="/shop" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-700 hover:bg-primary-800 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg">
                <i class="fas fa-store"></i> Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity update
    document.querySelectorAll('.qty-dec, .qty-inc').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('.qty-input');
            var val = parseInt(input.value) || 1;
            if (this.classList.contains('qty-dec') && val > 1) input.value = val - 1;
            if (this.classList.contains('qty-inc') && val < 99) input.value = val + 1;
            updateCartItem(input.dataset.itemId, input.value);
        });
    });
    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var val = parseInt(this.value) || 1;
            if (val < 1) this.value = 1;
            if (val > 99) this.value = 99;
            updateCartItem(this.dataset.itemId, this.value);
        });
    });

    function updateCartItem(itemId, qty) {
        fetch('/cart/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'item_id=' + itemId + '&quantity=' + qty + '&_csrf_token=<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>'
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) location.reload();
        }).catch(function() {});
    }

    // Remove item
    document.querySelectorAll('.remove-item-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Remove this item from cart?')) return;
            var itemId = this.dataset.itemId;
            fetch('/cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'item_id=' + itemId + '&_csrf_token=<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>'
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success) location.reload();
            }).catch(function() {});
        });
    });

    // Apply coupon
    document.getElementById('apply-coupon-btn')?.addEventListener('click', function() {
        var code = document.getElementById('coupon-input').value.trim();
        if (!code) return;
        var url = new URL(window.location.href);
        url.searchParams.set('coupon', code);
        window.location.href = url.toString();
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
