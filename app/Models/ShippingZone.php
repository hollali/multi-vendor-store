<?php
namespace App\Models;

use App\Core\Model;

class ShippingZone extends Model
{
    protected static string $table = 'shipping_zones';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name', 'description', 'type', 'countries', 'is_active'
    ];

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->orderBy('type', 'ASC')->get();
    }

    public static function findByType(string $type): array
    {
        return static::where('type', $type)->where('is_active', 1)->get();
    }
}
