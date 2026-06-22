<?php
namespace App\Models;

use App\Core\Model;

class Currency extends Model
{
    protected static string $table = 'currencies';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'code', 'name', 'symbol', 'decimal_places',
        'exchange_rate', 'is_base', 'is_active'
    ];

    public static function findByCode(string $code): ?\stdClass
    {
        return static::findBy('code', strtoupper($code));
    }

    public static function getBase(): ?\stdClass
    {
        return static::where('is_base', 1)->where('is_active', 1)->first();
    }

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->orderBy('code', 'ASC')->get();
    }
}
