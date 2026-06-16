<?php
namespace App\Models;

use App\Core\Model;

class OrderItem extends Model
{
    protected static string $table = 'order_items';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'quantity', 'price', 'total', 'variant_info'
    ];

    public function order(\stdClass $item): ?\stdClass
    {
        return Order::find($item->order_id);
    }

    public function product(\stdClass $item): ?\stdClass
    {
        return Product::find($item->product_id);
    }
}
