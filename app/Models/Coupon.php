<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Coupon extends Model
{
    protected static string $table = 'coupons';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_discount',
        'usage_limit', 'used_count', 'user_limit', 'starts_at',
        'expires_at', 'is_active', 'description'
    ];

    public static function findByCode(string $code): ?\stdClass
    {
        return static::where('code', $code)->first();
    }

    public function calculateDiscount(\stdClass $coupon, float $subtotal): float
    {
        if ($coupon->min_order_amount > 0 && $subtotal < (float)$coupon->min_order_amount) {
            return 0;
        }

        $discount = 0;

        if ($coupon->type === 'percentage') {
            $discount = $subtotal * ((float)$coupon->value / 100);
            if ((float)$coupon->max_discount > 0) {
                $discount = min($discount, (float)$coupon->max_discount);
            }
        } else {
            $discount = min((float)$coupon->value, $subtotal);
        }

        return round($discount, 2);
    }

    public function isValid(\stdClass $coupon): bool
    {
        if (!$coupon->is_active) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        if (!empty($coupon->starts_at) && $now < $coupon->starts_at) {
            return false;
        }

        if (!empty($coupon->expires_at) && $now > $coupon->expires_at) {
            return false;
        }

        if ((int)$coupon->usage_limit > 0 && (int)$coupon->used_count >= (int)$coupon->usage_limit) {
            return false;
        }

        return true;
    }

    public function incrementUsed(\stdClass $coupon): void
    {
        $db = Database::getInstance();
        $db->update(
            static::$table,
            ['used_count' => (int)$coupon->used_count + 1],
            'id = :id',
            ['id' => $coupon->id]
        );
    }
}
