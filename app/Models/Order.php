<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Order extends Model
{
    protected static string $table = 'orders';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'store_id', 'order_number', 'status', 'subtotal',
        'shipping_cost', 'tax', 'discount', 'coupon_id', 'total',
        'shipping_address', 'billing_address', 'payment_method',
        'payment_status', 'shipping_method', 'tracking_number',
        'notes', 'shipped_at', 'delivered_at', 'cancelled_at'
    ];

    public function user(\stdClass $order): ?\stdClass
    {
        return User::find($order->user_id);
    }

    public function items(\stdClass $order): array
    {
        return OrderItem::where('order_id', $order->id)->get();
    }

    public function payment(\stdClass $order): ?\stdClass
    {
        return Payment::where('order_id', $order->id)->first();
    }

    public static function generateOrderNumber(): string
    {
        $db = Database::getInstance();
        $prefix = 'ORD-' . date('Ymd') . '-';
        $result = $db->fetch(
            "SELECT COUNT(*) as cnt FROM orders WHERE order_number LIKE :prefix",
            ['prefix' => $prefix . '%']
        );
        $next = ((int)($result->cnt ?? 0)) + 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function scopeByUser(int $userId): array
    {
        return static::where('user_id', $userId)->orderBy('id', 'DESC')->get();
    }

    public static function scopeByStatus(string $status): array
    {
        return static::where('status', $status)->orderBy('id', 'DESC')->get();
    }

    public static function scopeRecent(int $limit = 10): array
    {
        return static::where('id', 0, '>')->orderBy('id', 'DESC')->limit($limit)->get();
    }
}
