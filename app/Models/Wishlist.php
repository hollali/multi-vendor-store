<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Wishlist extends Model
{
    protected static string $table = 'wishlists';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'product_id'
    ];

    public function product(\stdClass $wishlist): ?\stdClass
    {
        return Product::find($wishlist->product_id);
    }

    public static function toggle(int $userId, int $productId): bool
    {
        $existing = static::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            static::delete($existing->id);
            return false; // removed
        }

        static::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
        return true; // added
    }

    public static function isInWishlist(int $userId, int $productId): bool
    {
        $item = static::where('user_id', $userId)->where('product_id', $productId)->first();
        return $item !== null;
    }

    public static function getUserWishlist(int $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.* FROM wishlists w
             JOIN products p ON w.product_id = p.id
             WHERE w.user_id = :user_id
             ORDER BY w.id DESC",
            ['user_id' => $userId]
        );
    }
}
