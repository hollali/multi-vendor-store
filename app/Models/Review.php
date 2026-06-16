<?php
namespace App\Models;

use App\Core\Model;

class Review extends Model
{
    protected static string $table = 'reviews';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'product_id', 'order_id', 'rating', 'title', 'review', 'is_approved'
    ];

    public function user(\stdClass $review): ?\stdClass
    {
        return User::find($review->user_id);
    }

    public function product(\stdClass $review): ?\stdClass
    {
        return Product::find($review->product_id);
    }

    public static function scopeApproved(): array
    {
        return static::where('is_approved', 1)->get();
    }

    public static function scopeByProduct(int $productId): array
    {
        return static::where('product_id', $productId)->where('is_approved', 1)->orderBy('id', 'DESC')->get();
    }
}
