<?php
namespace App\Models;

use App\Core\Model;

class VendorShippingRate extends Model
{
    protected static string $table = 'vendor_shipping_rates';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'vendor_id', 'zone_id', 'base_rate', 'rate_per_kg',
        'free_shipping_min', 'estimated_days_min', 'estimated_days_max', 'is_active'
    ];

    public static function scopeForVendor(int $vendorId): array
    {
        return static::where('vendor_id', $vendorId)->where('is_active', 1)->get();
    }
}
