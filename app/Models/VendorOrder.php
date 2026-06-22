<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class VendorOrder extends Model
{
    protected static string $table = 'vendor_orders';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'parent_order_id', 'vendor_id', 'store_id', 'order_number',
        'subtotal', 'shipping_cost', 'tax', 'discount', 'total',
        'status', 'tracking_number', 'shipping_carrier', 'shipping_method',
        'estimated_delivery_min', 'estimated_delivery_max',
        'notes', 'shipped_at', 'delivered_at'
    ];

    public function parentOrder(\stdClass $vOrder): ?\stdClass
    {
        return Order::find($vOrder->parent_order_id);
    }

    public function vendor(\stdClass $vOrder): ?\stdClass
    {
        return User::find($vOrder->vendor_id);
    }

    public function items(\stdClass $vOrder): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM order_items WHERE vendor_order_id = :vorder_id",
            ['vorder_id' => $vOrder->id]
        );
    }

    public static function scopeByVendor(int $vendorId): array
    {
        return static::where('vendor_id', $vendorId)->orderBy('id', 'DESC')->get();
    }

    public static function scopeByParentOrder(int $orderId): array
    {
        return static::where('parent_order_id', $orderId)->get();
    }

    public function getDeliveryEstimate(\stdClass $vOrder): string
    {
        if ($vOrder->estimated_delivery_min && $vOrder->estimated_delivery_max) {
            return "{$vOrder->estimated_delivery_min}-{$vOrder->estimated_delivery_max} business days";
        }
        return 'Estimated delivery unavailable';
    }
}
