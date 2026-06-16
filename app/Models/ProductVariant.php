<?php
namespace App\Models;

use App\Core\Model;

class ProductVariant extends Model
{
    protected static string $table = 'product_variants';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'product_id', 'name', 'price_adjustment', 'stock',
        'sku', 'is_default', 'status'
    ];

    public function product(\stdClass $variant): ?\stdClass
    {
        return Product::find($variant->product_id);
    }

    public function values(\stdClass $variant): array
    {
        return ProductVariantValue::where('variant_id', $variant->id)->get();
    }
}
