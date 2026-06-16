<?php
namespace App\Models;

use App\Core\Model;

class CartItem extends Model
{
    protected static string $table = 'cart_items';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'cart_id', 'product_id', 'quantity', 'variant_info'
    ];

    public function product(\stdClass $item): ?\stdClass
    {
        return Product::find($item->product_id);
    }

    public function cart(\stdClass $item): ?\stdClass
    {
        return Cart::find($item->cart_id);
    }
}
