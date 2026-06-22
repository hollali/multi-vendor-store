<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Middleware;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\VendorOrder;
use App\Services\Geolocation;
use App\Services\Shipping;
use App\Services\OrderSplitter;

class CheckoutController extends Controller
{
    private function getPaystackConfig(): array
    {
        return require __DIR__ . '/../../config/paystack.php';
    }

    private function getOrCreateCart(): ?\stdClass
    {
        $db = Database::getInstance();
        $userId = $this->session->getUserId();
        $sessionId = session_id();

        if ($userId) {
            $cart = Cart::getCartForUser($userId);
            if ($cart) return $cart;
        }

        $cart = Cart::where('session_id', $sessionId)->first();
        if ($cart && $userId && !$cart->user_id) {
            $db->update('carts', ['user_id' => $userId, 'session_id' => null], 'id = :id', ['id' => $cart->id]);
        }
        return $cart;
    }

    public function index(): void
    {
        Middleware::auth();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();
        $cart = $this->getOrCreateCart();

        if (!$cart) {
            $this->redirectWith('/cart', 'Your cart is empty.', 'warning');
            return;
        }

        $this->initGeo();
        $countryCode = $this->geo->getCountryCode();

        $items = $db->fetchAll(
            "SELECT ci.*, p.name, p.slug, p.quantity as stock, p.weight_kg, p.ships_from_country, p.free_shipping,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as image,
                    s.store_name, s.slug as store_slug, s.id as store_id, s.vendor_id
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE ci.cart_id = :cart_id
             ORDER BY ci.id ASC",
            ['cart_id' => $cart->id]
        );

        if (empty($items)) {
            $this->redirectWith('/cart', 'Your cart is empty.', 'warning');
            return;
        }

        $subtotal = 0;
        $totalWeight = 0;
        $vendorGroups = [];

        foreach ($items as $item) {
            $itemTotal = (float)$item->unit_price * (int)$item->quantity;
            $subtotal += $itemTotal;
            $totalWeight += (float)($item->weight_kg ?? 0) * (int)$item->quantity;

            $vid = (int)$item->vendor_id;
            if (!isset($vendorGroups[$vid])) {
                $vendorGroups[$vid] = [
                    'vendor_id' => $vid,
                    'store_name' => $item->store_name ?? 'Unknown Store',
                    'store_slug' => $item->store_slug ?? '',
                    'items' => [],
                    'subtotal' => 0,
                    'total_weight' => 0,
                ];
            }
            $vendorGroups[$vid]['items'][] = $item;
            $vendorGroups[$vid]['subtotal'] += $itemTotal;
            $vendorGroups[$vid]['total_weight'] += (float)($item->weight_kg ?? 0) * (int)$item->quantity;
        }

        $discount = 0;
        $coupon = null;
        if ($cart->coupon_id) {
            $coupon = Coupon::find($cart->coupon_id);
            if ($coupon && Coupon::isValid($coupon)) {
                $couponModel = new Coupon();
                $discount = $couponModel->calculateDiscount($coupon, $subtotal);
            }
        }

        $shippingService = Shipping::getInstance();
        $vendorShippingInfo = [];

        foreach ($vendorGroups as $vid => $group) {
            $shippingInfo = $shippingService->getShippingRate(
                $vid,
                $countryCode,
                $group['subtotal'],
                $group['total_weight']
            );
            $vendorShippingInfo[$vid] = $shippingInfo;
        }

        $totalShipping = 0;
        foreach ($vendorShippingInfo as $info) {
            $totalShipping += $info['rate'];
        }

        $tax = round($subtotal * 0.025, 2);
        $total = max(0, $subtotal - $discount) + $totalShipping + $tax;

        $addresses = Address::where('user_id', $userId)->orderBy('is_default', 'DESC')->get();

        $defaultAddress = null;
        foreach ($addresses as $addr) {
            if ($addr->is_default) {
                $defaultAddress = $addr;
                break;
            }
        }

        $paystackConfig = $this->getPaystackConfig();
        $currencyData = $this->geo->getCurrencyData();

        $this->renderView('checkout/index', [
            'cart' => $cart,
            'items' => $items,
            'vendorGroups' => $vendorGroups,
            'vendorShippingInfo' => $vendorShippingInfo,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'totalShipping' => $totalShipping,
            'shippingCost' => $totalShipping,
            'tax' => $tax,
            'total' => $total,
            'coupon' => $coupon,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'paystackPublicKey' => $paystackConfig['public_key'],
            'countryCode' => $countryCode,
            'currencyCode' => $currencyData->code ?? 'GHS',
        ]);
    }

    public function placeOrder(): void
    {
        Middleware::auth();
        Middleware::csrf();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();
        $user = $this->currentUser;

        $this->initGeo();
        $countryCode = $this->geo->getCountryCode();
        $currencyData = $this->geo->getCurrencyData();
        $currencyCode = $currencyData->code ?? 'GHS';
        $currencySymbol = $currencyData->symbol ?? 'GH₵';

        $cart = $this->getOrCreateCart();
        if (!$cart) {
            $this->redirectWith('/cart', 'Your cart is empty.', 'error');
            return;
        }

        $items = $db->fetchAll(
            "SELECT ci.*, p.name, COALESCE(p.sale_price, p.base_price) as price, p.slug, p.sku, p.store_id, p.weight_kg,
                    s.store_name, s.vendor_id, s.commission_rate as store_commission
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE ci.cart_id = :cart_id",
            ['cart_id' => $cart->id]
        );

        if (empty($items)) {
            $this->redirectWith('/cart', 'Your cart is empty.', 'error');
            return;
        }

        $addressId = (int)$this->getParam('address_id', 0);
        $address = $addressId ? Address::find($addressId) : null;

        if (!$address) {
            $addresses = Address::where('user_id', $userId)->orderBy('is_default', 'DESC')->get();
            $address = $addresses[0] ?? null;
        }

        if (!$address) {
            $this->redirectWith('/checkout', 'Please add a shipping address.', 'error');
            return;
        }

        $shippingAddress = json_encode([
            'id' => $address->id,
            'label' => $address->label ?? '',
            'full_name' => $address->full_name ?? ($user->first_name . ' ' . $user->last_name) ?? '',
            'phone' => $address->phone ?? '',
            'street_address' => $address->street_address ?? '',
            'city' => $address->city ?? '',
            'state' => $address->state ?? '',
            'postal_code' => $address->postal_code ?? '',
            'country' => $address->country ?? 'Ghana',
            'country_code' => $countryCode,
        ]);

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float)$item->unit_price * (int)$item->quantity;
        }

        $discount = 0;
        $coupon = null;
        if ($cart->coupon_id) {
            $coupon = Coupon::find($cart->coupon_id);
            if ($coupon && $coupon->is_active) {
                $discount = ($coupon->type === 'percentage') ? min($subtotal * $coupon->value / 100, $coupon->max_discount ?? $subtotal) : min($coupon->value, $subtotal);
            }
        }

        $settings = $this->getSettings();
        $taxRate = (float)($settings['tax_rate'] ?? 2.5);
        $tax = round($subtotal * $taxRate / 100, 2);
        $notes = Validator::sanitizeString($this->getParam('notes', ''));

        $db->beginTransaction();

        try {
            $orderNumber = Order::generateOrderNumber();

            $orderId = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'tax' => $tax,
                'discount' => $discount,
                'coupon_code' => $coupon->code ?? null,
                'total' => $subtotal - $discount + $tax,
                'payment_method' => 'paystack',
                'payment_status' => 'pending',
                'notes' => $notes,
                'currency_code' => $currencyCode,
                'currency_symbol' => $currencySymbol,
                'exchange_rate' => $currencyData->exchange_rate ?? 1.000000,
                'shipping_country_code' => $countryCode,
            ]);

            foreach ($items as $item) {
                $itemTotal = (float)$item->unit_price * (int)$item->quantity;
                $commissionRate = (float)($item->store_commission ?? $settings['commission_rate'] ?? 10);
                $commissionAmount = round($itemTotal * $commissionRate / 100, 2);
                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->vendor_id,
                    'product_name' => $item->name,
                    'product_sku' => $item->sku ?? '',
                    'quantity' => (int)$item->quantity,
                    'unit_price' => (float)$item->unit_price,
                    'total_price' => $itemTotal,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'vendor_earnings' => $itemTotal - $commissionAmount,
                    'status' => 'pending',
                ]);
            }

            if ($coupon) {
                $coupon->incrementUsed($coupon);
            }

            $splitter = new OrderSplitter();
            $vendorOrders = $splitter->execute($orderId, $items, $countryCode, $discount, $coupon->code ?? null);

            $totalShipping = 0;
            foreach ($vendorOrders as $vo) {
                $totalShipping += $vo['shipping_cost'];
            }
            $grandTotal = max(0, $subtotal - $discount) + $totalShipping + $tax;
            Order::update($orderId, [
                'shipping_cost' => $totalShipping,
                'total' => $grandTotal,
            ]);

            $reference = 'CELER-' . strtoupper(bin2hex(random_bytes(8)));
            $paymentId = Payment::create([
                'order_id' => $orderId,
                'payment_method' => 'paystack',
                'payment_reference' => $reference,
                'amount' => $grandTotal,
                'currency' => $currencyCode,
                'status' => 'pending',
            ]);

            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            $this->redirectWith('/checkout', 'Failed to create order. Please try again.', 'error');
            return;
        }

        $paystackConfig = $this->getPaystackConfig();
        $amountInSmallest = (int)round($grandTotal * 100);

        $postData = [
            'email' => $user->email ?? $this->session->get('user_email', ''),
            'amount' => $amountInSmallest,
            'reference' => $reference,
            'callback_url' => $paystackConfig['callback_url'],
            'currency' => $paystackConfig['currency'],
            'metadata' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'country_code' => $countryCode,
                'currency_code' => $currencyCode,
            ],
        ];

        $ch = curl_init('https://api.paystack.co/transaction/initialize');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $paystackConfig['secret_key'],
                'Content-Type: application/json',
                'Cache-Control: no-cache',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            $db->update('payments', ['status' => 'failed'], 'id = :id', ['id' => $paymentId]);
            $db->update('orders', ['order_status' => 'failed'], 'id = :id', ['id' => $orderId]);
            $this->redirectWith('/checkout', 'Payment initialization failed. Please try again.', 'error');
            return;
        }

        $result = json_decode($response, true);
        if (!is_array($result) || !($result['status'] ?? false)) {
            $db->update('payments', ['status' => 'failed'], 'id = :id', ['id' => $paymentId]);
            $db->update('orders', ['order_status' => 'failed'], 'id = :id', ['id' => $orderId]);
            $this->redirectWith('/checkout', $result['message'] ?? 'Payment initialization failed.', 'error');
            return;
        }

        $this->session->set('pending_order_id', $orderId);

        $this->redirect($result['data']['authorization_url'] ?? '/checkout');
    }

    public function callback(): void
    {
        Middleware::auth();

        $reference = $this->getParam('reference', '');
        if (empty($reference)) {
            $this->redirectWith('/dashboard/orders', 'Invalid payment reference.', 'error');
            return;
        }

        $paystackConfig = $this->getPaystackConfig();

        $ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $paystackConfig['secret_key'],
                'Cache-Control: no-cache',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->redirectWith('/dashboard/orders', 'Could not verify payment. Please contact support.', 'error');
            return;
        }

        $result = json_decode($response, true);
        if (!is_array($result) || !($result['status'] ?? false) || !($result['data']['status'] ?? false)) {
            $this->redirectWith('/dashboard/orders', 'Payment verification failed.', 'error');
            return;
        }

        $paymentData = $result['data'];
        $db = Database::getInstance();

        $payment = $db->fetch("SELECT * FROM payments WHERE paystack_reference = :ref OR payment_reference = :ref2 LIMIT 1",
            ['ref' => $reference, 'ref2' => $reference]
        );
        if (!$payment) {
            $this->redirectWith('/dashboard/orders', 'Payment record not found.', 'error');
            return;
        }

        $orderId = $payment->order_id;
        $order = Order::find($orderId);
        if (!$order) {
            $this->redirectWith('/dashboard/orders', 'Order not found.', 'error');
            return;
        }

        if ($paymentData['status'] === 'success') {
            $db->update('payments', [
                'status' => 'success',
                'paid_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $payment->id]);

            $db->update('orders', [
                'order_status' => 'processing',
                'payment_status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $orderId]);

            $db->update('vendor_orders', [
                'status' => 'processing',
            ], 'parent_order_id = :oid', ['oid' => $orderId]);

            $cart = Cart::getCartForUser($this->session->getUserId());
            if ($cart) {
                $db->delete('cart_items', 'cart_id = :cart_id', ['cart_id' => $cart->id]);
                $db->update('carts', ['coupon_id' => null], 'id = :id', ['id' => $cart->id]);
            }

            $this->session->remove('pending_order_id');
            $this->redirectWith('/dashboard/orders/' . $orderId, 'Payment successful! Your order has been placed.', 'success');
        } else {
            $db->update('payments', ['status' => 'failed'], 'id = :id', ['id' => $payment->id]);
            $db->update('orders', ['order_status' => 'failed', 'payment_status' => 'failed'], 'id = :id', ['id' => $orderId]);

            $this->redirectWith('/checkout', 'Payment was not successful. Please try again.', 'error');
        }
    }

    public function webhook(): void
    {
        $input = file_get_contents('php://input');
        $payload = json_decode($input, true);

        if (!$payload || !isset($payload['event'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid payload']);
            exit;
        }

        $paystackConfig = $this->getPaystackConfig();

        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
        $expectedSignature = hash_hmac('sha512', $input, $paystackConfig['webhook_secret']);

        if (!hash_equals($expectedSignature, $signature)) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
            exit;
        }

        $event = $payload['event'];
        $data = $payload['data'] ?? [];

        if ($event === 'charge.success') {
            $reference = $data['reference'] ?? '';
            if (empty($reference)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing reference']);
                exit;
            }

            $db = Database::getInstance();
            $payment = $db->fetch(
                "SELECT * FROM payments WHERE paystack_reference = :ref OR payment_reference = :ref2 LIMIT 1",
                ['ref' => $reference, 'ref2' => $reference]
            );

            if (!$payment) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found']);
                exit;
            }

            if ($payment->status === 'success') {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Already processed']);
                exit;
            }

            $amountPaid = ($data['amount'] ?? 0) / 100;
            $expectedAmount = (float)$payment->amount;

            if (abs($amountPaid - $expectedAmount) > 0.01) {
                $db->update('payments', [
                    'status' => 'failed',
                ], 'id = :id', ['id' => $payment->id]);

                http_response_code(200);
                echo json_encode(['status' => 'error', 'message' => 'Amount mismatch']);
                exit;
            }

            $db->beginTransaction();
            try {
                $db->update('payments', [
                    'status' => 'success',
                    'paid_at' => date('Y-m-d H:i:s'),
                ], 'id = :id', ['id' => $payment->id]);

                $db->update('orders', [
                    'order_status' => 'processing',
                    'payment_status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                ], 'id = :id', ['id' => $payment->order_id]);

                $db->update('vendor_orders', [
                    'status' => 'processing',
                ], 'parent_order_id = :oid', ['oid' => $payment->order_id]);

                $db->commit();
            } catch (\Exception $e) {
                $db->rollback();
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
                exit;
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit;
    }
}
