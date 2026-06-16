<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Cart extends Model
{
    protected static string $table = 'carts';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'session_id', 'coupon_id', 'notes'
    ];

    public function items(\stdClass $cart): array
    {
        return CartItem::where('cart_id', $cart->id)->get();
    }

    public function getItemCount(\stdClass $cart): int
    {
        $db = Database::getInstance();
        $result = $db->fetch(
            "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id",
            ['cart_id' => $cart->id]
        );
        return (int)($result->total ?? 0);
    }

    public static function getCartForUser(int $userId): ?\stdClass
    {
        return static::where('user_id', $userId)->first();
    }

    public function calculateTotals(\stdClass $cart): array
    {
        $db = Database::getInstance();
        $items = $this->items($cart);
        $subtotal = 0;
        $totalItems = 0;

        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $price = (float)($product->price ?? 0);
                $qty = (int)($item->quantity ?? 1);
                $subtotal += $price * $qty;
                $totalItems += $qty;
            }
        }

        return [
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
            'item_count' => $totalItems,
        ];
    }

    public static function addItem(int $cartId, int $productId, int $quantity = 1, ?array $variantInfo = null): int
    {
        $existing = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->first();

        if ($existing) {
            $newQty = (int)$existing->quantity + $quantity;
            CartItem::update($existing->id, ['quantity' => $newQty]);
            return $existing->id;
        }

        $data = [
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ];

        if ($variantInfo !== null) {
            $data['variant_info'] = json_encode($variantInfo);
        }

        return CartItem::create($data);
    }

    public static function removeItem(int $cartId, int $productId): void
    {
        $existing = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->first();
        if ($existing) {
            CartItem::delete($existing->id);
        }
    }

    public function clear(\stdClass $cart): void
    {
        $db = Database::getInstance();
        $db->delete('cart_items', 'cart_id = :cart_id', ['cart_id' => $cart->id]);
    }
}
