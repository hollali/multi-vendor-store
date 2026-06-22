<?php
namespace App\Models;

use App\Core\Model;

class Country extends Model
{
    protected static string $table = 'countries';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'code', 'name', 'phone_code', 'currency_code',
        'region', 'is_active', 'sort_order'
    ];

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();
    }

    public static function findByCode(string $code): ?\stdClass
    {
        return static::findBy('code', strtoupper($code));
    }
}
