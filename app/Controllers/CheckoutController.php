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

        $items = $db->fetchAll(
            "SELECT ci.*, p.name, p.slug, p.quantity as stock,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as image,
                    s.store_name, s.slug as store_slug
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
        foreach ($items as $item) {
            $subtotal += (float)$item->unit_price * (int)$item->quantity;
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

        $shippingCost = $subtotal >= 200 ? 0 : 15;
        $tax = round($subtotal * 0.025, 2);
        $total = max(0, $subtotal - $discount) + $shippingCost + $tax;

        $addresses = Address::where('user_id', $userId)->orderBy('is_default', 'DESC')->get();

        $defaultAddress = null;
        foreach ($addresses as $addr) {
            if ($addr->is_default) {
                $defaultAddress = $addr;
                break;
            }
        }

        $paystackConfig = $this->getPaystackConfig();

        $this->renderView('checkout/index', [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shippingCost' => $shippingCost,
            'tax' => $tax,
            'total' => $total,
            'coupon' => $coupon,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'paystackPublicKey' => $paystackConfig['public_key'],
        ]);
    }

    public function placeOrder(): void
    {
        Middleware::auth();
        Middleware::csrf();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();
        $user = $this->currentUser;

        $cart = $this->getOrCreateCart();
        if (!$cart) {
            $this->redirectWith('/cart', 'Your cart is empty.', 'error');
            return;
        }

        $items = $db->fetchAll(
            "SELECT ci.*, p.name, COALESCE(p.sale_price, p.base_price) as price, p.slug, p.sku, p.store_id, s.store_name, p.vendor_id
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
        $shippingCost = (float)($settings['shipping_fee'] ?? 5000);
        $taxRate = (float)($settings['tax_rate'] ?? 7.5);
        $tax = round($subtotal * $taxRate / 100, 2);
        $total = max(0, $subtotal - $discount) + $shippingCost + $tax;

        $notes = Validator::sanitizeString($this->getParam('notes', ''));

        $db->beginTransaction();

        try {
            $orderNumber = Order::generateOrderNumber();

            $orderId = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'discount' => $discount,
                'coupon_code' => $coupon->code ?? null,
                'total' => $total,
                'payment_method' => 'paystack',
                'payment_status' => 'pending',
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $itemTotal = (float)$item->unit_price * (int)$item->quantity;
                $commissionRate = (float)($settings['commission_rate'] ?? 10);
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

            $reference = 'CELER-' . strtoupper(bin2hex(random_bytes(8)));
            $paymentId = Payment::create([
                'order_id' => $orderId,
                'payment_method' => 'paystack',
                'payment_reference' => $reference,
                'amount' => $total,
                'currency' => 'GHS',
                'status' => 'pending',
            ]);

            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            $this->redirectWith('/checkout', 'Failed to create order. Please try again.', 'error');
            return;
        }

        $paystackConfig = $this->getPaystackConfig();
        $amountInPesewas = (int)round($total * 100);

        $postData = [
            'email' => $user->email ?? $this->session->get('user_email', ''),
            'amount' => $amountInPesewas,
            'reference' => $reference,
            'callback_url' => $paystackConfig['callback_url'],
            'currency' => $paystackConfig['currency'],
            'metadata' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'user_id' => $userId,
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
            $db->update('orders', ['status' => 'failed'], 'id = :id', ['id' => $orderId]);
            $this->redirectWith('/checkout', 'Payment initialization failed. Please try again.', 'error');
            return;
        }

        $result = json_decode($response, true);
        if (!is_array($result) || !($result['status'] ?? false)) {
            $db->update('payments', ['status' => 'failed'], 'id = :id', ['id' => $paymentId]);
            $db->update('orders', ['status' => 'failed'], 'id = :id', ['id' => $orderId]);
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
            $this->redirectWith('/orders', 'Invalid payment reference.', 'error');
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
            $this->redirectWith('/orders', 'Could not verify payment. Please contact support.', 'error');
            return;
        }

        $result = json_decode($response, true);
        if (!is_array($result) || !($result['status'] ?? false) || !($result['data']['status'] ?? false)) {
            $this->redirectWith('/orders', 'Payment verification failed.', 'error');
            return;
        }

        $paymentData = $result['data'];
        $db = Database::getInstance();

        $payment = Payment::scopeByReference($reference);
        if (!$payment) {
            $this->redirectWith('/orders', 'Payment record not found.', 'error');
            return;
        }

        $orderId = $payment->order_id;
        $order = Order::find($orderId);
        if (!$order) {
            $this->redirectWith('/orders', 'Order not found.', 'error');
            return;
        }

        if ($paymentData['status'] === 'success') {
            $db->update('payments', [
                'status' => 'completed',
                'paid_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $payment->id]);

            $db->update('orders', [
                'status' => 'processing',
                'payment_status' => 'paid',
            ], 'id = :id', ['id' => $orderId]);

            $cart = Cart::getCartForUser($this->session->getUserId());
            if ($cart) {
                $db->delete('cart_items', 'cart_id = :cart_id', ['cart_id' => $cart->id]);
                $db->update('carts', ['coupon_id' => null], 'id = :id', ['id' => $cart->id]);
            }

            $this->session->remove('pending_order_id');
            $this->redirectWith('/orders/' . $orderId, 'Payment successful! Your order has been placed.', 'success');
        } else {
            $db->update('payments', [
                'status' => 'failed',
            ], 'id = :id', ['id' => $payment->id]);

            $db->update('orders', [
                'status' => 'failed',
                'payment_status' => 'failed',
            ], 'id = :id', ['id' => $orderId]);

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
            $status = $data['status'] ?? '';

            if (empty($reference)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing reference']);
                exit;
            }

            $db = Database::getInstance();
            $payment = Payment::scopeByReference($reference);

            if (!$payment) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found']);
                exit;
            }

            if ($payment->status === 'completed') {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Already processed']);
                exit;
            }

            $amountPaid = ($data['amount'] ?? 0) / 100;
            $expectedAmount = (float)$payment->amount;

            if (abs($amountPaid - $expectedAmount) > 0.01) {
                $db->update('payments', [
                    'status' => 'failed',
                    'notes' => 'Amount mismatch: paid ' . $amountPaid . ', expected ' . $expectedAmount,
                ], 'id = :id', ['id' => $payment->id]);

                http_response_code(200);
                echo json_encode(['status' => 'error', 'message' => 'Amount mismatch']);
                exit;
            }

            $db->beginTransaction();
            try {
                $db->update('payments', [
                    'status' => 'completed',
                    'paid_at' => date('Y-m-d H:i:s'),
                ], 'id = :id', ['id' => $payment->id]);

                $db->update('orders', [
                    'status' => 'processing',
                    'payment_status' => 'paid',
                ], 'id = :id', ['id' => $payment->order_id]);

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
