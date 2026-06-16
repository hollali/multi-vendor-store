<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Store extends Model
{
    protected static string $table = 'stores';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'vendor_id', 'store_name', 'slug', 'description', 'logo', 'banner',
        'phone', 'email', 'address', 'city', 'state', 'country',
        'tax_id', 'registration_number', 'commission_rate',
        'is_verified', 'is_active'
    ];

    public function vendor(\stdClass $store): ?\stdClass
    {
        return User::find($store->vendor_id);
    }

    public function products(\stdClass $store): array
    {
        return Product::where('store_id', $store->id)->get();
    }

    public function getProductCount(\stdClass $store): int
    {
        $db = Database::getInstance();
        $result = $db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE store_id = :store_id",
            ['store_id' => $store->id]
        );
        return (int)($result->count ?? 0);
    }

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->get();
    }

    public static function scopeVerified(): array
    {
        return static::where('is_verified', 1)->get();
    }

    public function getRating(\stdClass $store): float
    {
        $db = Database::getInstance();
        $result = $db->fetch(
            "SELECT AVG(r.rating) as avg_rating FROM reviews r
             JOIN products p ON r.product_id = p.id
             WHERE p.store_id = :store_id AND r.is_approved = 1",
            ['store_id' => $store->id]
        );
        return round((float)($result->avg_rating ?? 0), 1);
    }
}
