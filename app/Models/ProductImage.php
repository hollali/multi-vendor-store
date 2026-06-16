<?php
namespace App\Models;

use App\Core\Model;

class ProductImage extends Model
{
    protected static string $table = 'product_images';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'product_id', 'image', 'alt_text', 'sort_order', 'is_primary'
    ];

    public function product(\stdClass $image): ?\stdClass
    {
        return Product::find($image->product_id);
    }

    public static function scopePrimary(): array
    {
        return static::where('is_primary', 1)->get();
    }
}
