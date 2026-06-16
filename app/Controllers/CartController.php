<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Middleware;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;

class CartController extends Controller
{
    private function getOrCreateCart(): \stdClass
    {
        $db = Database::getInstance();
        $userId = $this->session->getUserId();
        $sessionId = session_id();

        if ($userId) {
            $cart = Cart::getCartForUser($userId);
            if ($cart) {
                if ($cart->session_id && $cart->session_id !== $sessionId) {
                    $db->update('carts', ['session_id' => null], 'id = :id', ['id' => $cart->id]);
                }
                return $cart;
            }
        }

        $cart = Cart::where('session_id', $sessionId)->first();
        if ($cart) {
            if ($userId && !$cart->user_id) {
                $db->update('carts', ['user_id' => $userId, 'session_id' => null], 'id = :id', ['id' => $cart->id]);
            }
            return $cart;
        }

        $cartId = Cart::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
        ]);

        $cart = Cart::find($cartId);
        return $cart;
    }

    public function index(): void
    {
        $cart = $this->getOrCreateCart();
        $items = [];
        $subtotal = 0;
        $totalItems = 0;

        if ($cart) {
            $db = Database::getInstance();
            $cartItems = $db->fetchAll(
                "SELECT ci.*, p.name, p.slug, p.quantity as stock,
                        (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as image
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.id
                 WHERE ci.cart_id = :cart_id
                 ORDER BY ci.id ASC",
                ['cart_id' => $cart->id]
            );

            foreach ($cartItems as $item) {
                $itemTotal = (float)$item->unit_price * (int)$item->quantity;
                $subtotal += $itemTotal;
                $totalItems += (int)$item->quantity;
                $items[] = $item;
            }
        }

        $discount = 0;
        $coupon = null;
        if ($cart && $cart->coupon_id) {
            $coupon = Coupon::find($cart->coupon_id);
            if ($coupon && Coupon::isValid($coupon)) {
                $couponModel = new Coupon();
                $discount = $couponModel->calculateDiscount($coupon, $subtotal);
            } else {
                $db = Database::getInstance();
                $db->update('carts', ['coupon_id' => null], 'id = :id', ['id' => $cart->id]);
                $coupon = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        $this->renderView('cart/index', [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'totalItems' => $totalItems,
            'coupon' => $coupon,
        ]);
    }

    public function add(): void
    {
        Middleware::csrf();

        $productId = (int)$this->getParam('product_id', 0);
        $quantity = max(1, (int)$this->getParam('quantity', 1));
        $variantId = (int)$this->getParam('variant_id', 0);
        $redirect = (bool)$this->getParam('redirect', false);

        $product = Product::find($productId);
        if (!$product || !$product->is_active || !$product->is_approved) {
            if ($this->isAjax()) {
                $this->renderJSON(['success' => false, 'message' => 'Product not found.'], 404);
            }
            $this->redirectWith('/shop', 'Product not found.', 'error');
            return;
        }

        if ((int)$product->quantity < $quantity) {
            if ($this->isAjax()) {
                $this->renderJSON(['success' => false, 'message' => 'Product is out of stock.']);
            }
            $this->redirectWith('/shop/' . $product->slug, 'Product is out of stock.', 'error');
            return;
        }

        $unitPrice = $product->sale_price ?? $product->base_price;

        $cart = $this->getOrCreateCart();

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'variant_id' => $variantId ?: null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
        ]);

        $db = Database::getInstance();
        $itemCount = $db->fetch(
            "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id",
            ['cart_id' => $cart->id]
        );
        $count = (int)($itemCount->total ?? 0);

        if ($this->isAjax()) {
            $this->renderJSON([
                'success' => true,
                'message' => 'Item added to cart.',
                'cart_count' => $count,
                'cart_total' => $this->formatPrice($this->calculateCartSubtotal($cart)),
            ]);
        }

        $this->redirectWith('/cart', 'Item added to cart.', 'success');
    }

    public function update(): void
    {
        Middleware::csrf();

        $itemId = (int)$this->getParam('item_id', 0);
        $quantity = max(0, (int)$this->getParam('quantity', 0));

        $cart = $this->getOrCreateCart();
        $db = Database::getInstance();

        $item = $db->fetch(
            "SELECT ci.* FROM cart_items ci WHERE ci.id = :id AND ci.cart_id = :cart_id",
            ['id' => $itemId, 'cart_id' => $cart->id]
        );

        if (!$item) {
            $this->renderJSON(['success' => false, 'message' => 'Cart item not found.'], 404);
            return;
        }

        if ($quantity <= 0) {
            CartItem::delete($itemId);
            $message = 'Item removed from cart.';
        } else {
            CartItem::update($itemId, ['quantity' => $quantity]);
            $message = 'Cart updated.';
        }

        $items = $db->fetchAll(
            "SELECT ci.*, COALESCE(p.sale_price, p.base_price) as price
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id = :cart_id",
            ['cart_id' => $cart->id]
        );

        $subtotal = 0;
        $totalItems = 0;
        foreach ($items as $i) {
            $subtotal += (float)$i->price * (int)$i->quantity;
            $totalItems += (int)$i->quantity;
        }

        $this->renderJSON([
            'success' => true,
            'message' => $message,
            'cart_count' => $totalItems,
            'subtotal' => $this->formatPrice($subtotal),
            'subtotal_raw' => $subtotal,
        ]);
    }

    public function remove(): void
    {
        Middleware::csrf();

        $itemId = (int)$this->getParam('item_id', 0);

        $cart = $this->getOrCreateCart();
        $db = Database::getInstance();

        $item = $db->fetch(
            "SELECT ci.* FROM cart_items ci WHERE ci.id = :id AND ci.cart_id = :cart_id",
            ['id' => $itemId, 'cart_id' => $cart->id]
        );

        if (!$item) {
            $this->renderJSON(['success' => false, 'message' => 'Cart item not found.'], 404);
            return;
        }

        CartItem::delete($itemId);

        $itemCount = $db->fetch(
            "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id",
            ['cart_id' => $cart->id]
        );
        $count = (int)($itemCount->total ?? 0);

        $this->renderJSON([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $count,
            'subtotal' => $this->formatPrice($this->calculateCartSubtotal($cart)),
        ]);
    }

    public function applyCoupon(): void
    {
        Middleware::csrf();

        $code = trim($this->getParam('code', ''));
        if (empty($code)) {
            $this->renderJSON(['success' => false, 'message' => 'Please enter a coupon code.']);
            return;
        }

        $coupon = Coupon::findByCode($code);
        if (!$coupon) {
            $this->renderJSON(['success' => false, 'message' => 'Invalid coupon code.']);
            return;
        }

        if (!Coupon::isValid($coupon)) {
            $this->renderJSON(['success' => false, 'message' => 'This coupon has expired or is no longer valid.']);
            return;
        }

        $cart = $this->getOrCreateCart();
        $subtotal = $this->calculateCartSubtotal($cart);

        if ((float)$coupon->min_order_amount > 0 && $subtotal < (float)$coupon->min_order_amount) {
            $this->renderJSON([
                'success' => false,
                'message' => 'Minimum order amount of ' . $this->formatPrice($coupon->min_order_amount) . ' required.',
            ]);
            return;
        }

        $db = Database::getInstance();
        $db->update('carts', ['coupon_id' => $coupon->id], 'id = :id', ['id' => $cart->id]);

        $couponModel = new Coupon();
        $discount = $couponModel->calculateDiscount($coupon, $subtotal);
        $total = max(0, $subtotal - $discount);

        $this->renderJSON([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $this->formatPrice($discount),
            'discount_raw' => $discount,
            'total' => $this->formatPrice($total),
            'total_raw' => $total,
            'subtotal' => $this->formatPrice($subtotal),
        ]);
    }

    private function calculateCartSubtotal(\stdClass $cart): float
    {
        $db = Database::getInstance();
        $items = $db->fetchAll(
            "SELECT ci.quantity, COALESCE(p.sale_price, p.base_price) as price
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id = :cart_id",
            ['cart_id' => $cart->id]
        );

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float)$item->price * (int)$item->quantity;
        }
        return $subtotal;
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
