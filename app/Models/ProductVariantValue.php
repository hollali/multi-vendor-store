<?php
namespace App\Models;

use App\Core\Model;

class ProductVariantValue extends Model
{
    protected static string $table = 'product_variant_values';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'variant_id', 'attribute_name', 'attribute_value'
    ];

    public function variant(\stdClass $value): ?\stdClass
    {
        return ProductVariant::find($value->variant_id);
    }
}
