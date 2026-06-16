<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Product extends Model
{
    protected static string $table = 'products';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'vendor_id', 'store_id', 'category_id', 'brand_id', 'name', 'slug', 'sku',
        'description', 'short_description', 'specifications',
        'base_price', 'sale_price', 'discount_percent', 'quantity', 'min_quantity',
        'weight', 'is_featured', 'is_active', 'is_approved', 'status', 'rejection_reason'
    ];

    public function category(\stdClass $product): ?\stdClass
    {
        return Category::find($product->category_id);
    }

    public function brand(\stdClass $product): ?\stdClass
    {
        return Brand::find($product->brand_id);
    }

    public function store(\stdClass $product): ?\stdClass
    {
        return Store::find($product->store_id);
    }

    public function vendor(\stdClass $product): ?\stdClass
    {
        return User::find($product->vendor_id);
    }

    public function images(\stdClass $product): array
    {
        return ProductImage::where('product_id', $product->id)->orderBy('sort_order', 'ASC')->get();
    }

    public function primaryImage(\stdClass $product): ?\stdClass
    {
        return ProductImage::where('product_id', $product->id)->where('is_primary', 1)->first();
    }

    public function reviews(\stdClass $product): array
    {
        return Review::where('product_id', $product->id)->where('is_approved', 1)->get();
    }

    public function getPrice(\stdClass $product): float
    {
        return (float)($product->sale_price ?? $product->base_price ?? 0);
    }

    public function getOriginalPrice(\stdClass $product): float
    {
        return (float)($product->base_price ?? 0);
    }

    public function getDiscountPercent(\stdClass $product): int
    {
        if (empty($product->base_price) || $product->base_price <= 0 || empty($product->sale_price)) {
            return 0;
        }
        return (int)round((1 - $product->sale_price / $product->base_price) * 100);
    }

    public function getAverageRating(\stdClass $product): float
    {
        $db = Database::getInstance();
        $result = $db->fetch(
            "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = :id AND is_approved = 1",
            ['id' => $product->id]
        );
        return round((float)($result->avg_rating ?? 0), 1);
    }

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->where('is_approved', 1)->get();
    }

    public static function scopeFeatured(): array
    {
        return static::where('is_featured', 1)->where('is_active', 1)->where('is_approved', 1)->get();
    }

    public static function search(string $term): array
    {
        $db = Database::getInstance();
        $like = '%' . $term . '%';
        return $db->fetchAll(
            "SELECT p.* FROM products p WHERE p.is_active = 1 AND p.is_approved = 1 AND (p.name LIKE :term OR p.description LIKE :term) ORDER BY p.id DESC",
            ['term' => $like]
        );
    }
}
