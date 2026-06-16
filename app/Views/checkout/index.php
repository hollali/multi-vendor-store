<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$cartItems = $cartItems ?? $items ?? $cart ?? [];
$subtotal = (float)($subtotal ?? 0);
$discount = (float)($discount ?? 0);
$tax = (float)($tax ?? 0);
$shipping = (float)($shipping ?? 0);
$finalTotal = (float)($finalTotal ?? $subtotal - $discount + $tax + $shipping);
$currencySymbol = 'GH₵';
$hasItems = !empty($cartItems) && (is_array($cartItems) || count($cartItems) > 0);
$paystackPublicKey = htmlspecialchars($paystackPublicKey ?? $_ENV['PAYSTACK_PUBLIC_KEY'] ?? '');
$paymentReference = $paymentReference ?? 'CELER-' . strtoupper(uniqid());
$userAddresses = $userAddresses ?? [];
$userEmail = $user->email ?? $user['email'] ?? '';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6">
        <a href="/" class="hover:text-primary-700 dark:hover:text-primary-400 transition"><i class="fas fa-home mr-1"></i>Home</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <a href="/cart" class="hover:text-primary-700 dark:hover:text-primary-400 transition">Cart</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-800 dark:text-gray-200 font-medium">Checkout</span>
    </nav>

    <?php if ($hasItems): ?>
        <form id="checkout-form" action="/checkout/place-order" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
            <input type="hidden" name="payment_reference" value="<?= $paymentReference ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Form -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Shipping Address -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-primary-600"></i> Shipping Address
                        </h3>
                        <?php if (!empty($userAddresses) && (is_array($userAddresses) || count($userAddresses) > 0)): ?>
                            <div class="space-y-3 mb-4">
                                <?php foreach ($userAddresses as $addr): ?>
                                    <?php
                                    $addrId = $addr->id ?? $addr['id'] ?? 0;
                                    $addrLabel = $addr->label ?? $addr['label'] ?? 'Address';
                                    $addrLine = $addr->address ?? $addr['address'] ?? '';
                                    $addrCity = $addr->city ?? $addr['city'] ?? '';
                                    $addrRegion = $addr->region ?? $addr['region'] ?? '';
                                    $addrPhone = $addr->phone ?? $addr['phone'] ?? '';
                                    $isDefault = $addr->is_default ?? $addr['is_default'] ?? false;
                                    ?>
                                    <label class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/20">
                                        <input type="radio" name="address_id" value="<?= $addrId ?>" <?= $isDefault ? 'checked' : '' ?> class="mt-0.5 text-primary-700 focus:ring-primary-500">
                                        <div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200"><?= htmlspecialchars($addrLabel) ?></span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($addrLine) ?>, <?= htmlspecialchars($addrCity) ?>, <?= htmlspecialchars($addrRegion) ?></p>
                                            <?php if ($addrPhone): ?>
                                                <p class="text-xs text-gray-400 mt-0.5"><i class="fas fa-phone mr-1"></i><?= htmlspecialchars($addrPhone) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <p class="text-xs text-gray-400 mb-4">— or —</p>
                        <?php endif; ?>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                                <input type="text" name="shipping_name" required value="<?= htmlspecialchars($user->name ?? $user['name'] ?? '') ?>" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone Number</label>
                                <input type="tel" name="shipping_phone" required value="<?= htmlspecialchars($user->phone ?? $user['phone'] ?? '') ?>" placeholder="+233 XX XXX XXXX" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                                <input type="text" name="shipping_address" required placeholder="Street, building, house number" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">City</label>
                                <input type="text" name="shipping_city" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Region</label>
                                <select name="shipping_region" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200">
                                    <option value="">Select Region</option>
                                    <option value="Greater Accra">Greater Accra</option>
                                    <option value="Ashanti">Ashanti</option>
                                    <option value="Western">Western</option>
                                    <option value="Eastern">Eastern</option>
                                    <option value="Northern">Northern</option>
                                    <option value="Central">Central</option>
                                    <option value="Volta">Volta</option>
                                    <option value="Bono">Bono</option>
                                    <option value="Upper East">Upper East</option>
                                    <option value="Upper West">Upper West</option>
                                    <option value="Oti">Oti</option>
                                    <option value="Ahafo">Ahafo</option>
                                    <option value="Bono East">Bono East</option>
                                    <option value="Savannah">Savannah</option>
                                    <option value="North East">North East</option>
                                    <option value="Western North">Western North</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-box text-primary-600"></i> Order Items
                        </h3>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($cartItems as $item): ?>
                                <?php
                                $ciName = $item->name ?? $item['name'] ?? ($item->product->name ?? $item['product']['name'] ?? 'Product');
                                $ciSlug = $item->slug ?? $item['slug'] ?? ($item->product->slug ?? $item['product']['slug'] ?? '');
                                $ciImage = $item->image ?? $item['image'] ?? ($item->product->image ?? $item['product']['image'] ?? '');
                                $ciPrice = (float)($item->price ?? $item['price'] ?? 0);
                                $ciQuantity = (int)($item->quantity ?? $item['quantity'] ?? 1);
                                $ciVariant = $item->variant ?? $item['variant'] ?? '';
                                $ciTotal = $ciPrice * $ciQuantity;
                                ?>
                                <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                        <?php if ($ciImage): ?>
                                            <img src="<?= htmlspecialchars($ciImage) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-300 dark:text-gray-600"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($ciName) ?></p>
                                        <?php if ($ciVariant): ?>
                                            <p class="text-xs text-gray-400"><?= htmlspecialchars($ciVariant) ?></p>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-400">Qty: <?= $ciQuantity ?></p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= $currencySymbol ?><?= number_format($ciTotal, 2) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-credit-card text-primary-600"></i> Payment Method
                        </h3>
                        <label class="flex items-center gap-4 p-4 border border-primary-300 dark:border-primary-700 rounded-lg bg-primary-50 dark:bg-primary-900/20 cursor-pointer">
                            <input type="radio" name="payment_method" value="paystack" checked class="text-primary-700 focus:ring-primary-500">
                            <div class="flex items-center gap-3">
                                <img src="https://paystack.com/assets/img/brand/logo.svg" alt="Paystack" class="h-8">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Pay with Paystack</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Cards, Mobile Money, Bank Transfer</p>
                                </div>
                            </div>
                        </label>
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                            <span><i class="fas fa-lock mr-1"></i> Secured by Paystack</span>
                            <span><i class="fas fa-shield-alt mr-1"></i> SSL Encrypted</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Summary -->
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
                            <div class="flex items-center justify-between text-lg font-bold text-gray-900 dark:text-white">
                                <span>Total</span>
                                <span><?= $currencySymbol ?><?= number_format($finalTotal, 2) ?></span>
                            </div>
                        </div>
                        <button type="submit" id="place-order-btn" class="mt-5 w-full py-3.5 bg-accent-600 hover:bg-accent-700 text-white font-bold rounded-lg transition shadow-lg hover:shadow-xl text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-lock text-xs"></i> Place Order
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-3">By placing this order, you agree to our <a href="/terms" class="text-primary-600 hover:underline">Terms of Service</a>.</p>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-gray-100 dark:bg-gray-700 mb-5">
                <i class="fas fa-shopping-cart text-5xl text-gray-300 dark:text-gray-500"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Your cart is empty</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Add some items to your cart before checking out.</p>
            <a href="/shop" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-700 hover:bg-primary-800 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg"><i class="fas fa-store"></i> Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php if ($hasItems && $paystackPublicKey): ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
    e.preventDefault();

    var form = this;
    var btn = document.getElementById('place-order-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    var total = <?= $finalTotal * 100 ?>; // Paystack expects amount in pesewas/kobo
    var ref = '<?= $paymentReference ?>';
    var email = '<?= htmlspecialchars($userEmail) ?>';

    var handler = PaystackPop.setup({
        key: '<?= $paystackPublicKey ?>',
        email: email,
        amount: Math.round(total),
        currency: 'GHS',
        ref: ref,
        metadata: {
            custom_fields: [
                { display_name: "Source", variable_name: "source", value: "Celer Market" }
            ]
        },
        callback: function(response) {
            // Add payment reference to form and submit
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'paystack_ref';
            input.value = response.reference;
            form.appendChild(input);
            form.submit();
        },
        onClose: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock text-xs"></i> Place Order';
        }
    });

    handler.openIframe();
});
</script>
<?php elseif ($hasItems && !$paystackPublicKey): ?>
<script>
document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
    // No Paystack key configured, submit normally
});
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
