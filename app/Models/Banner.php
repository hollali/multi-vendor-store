<?php
namespace App\Models;

use App\Core\Model;

class Banner extends Model
{
    protected static string $table = 'banners';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'title', 'subtitle', 'link', 'image', 'sort_order', 'is_active'
    ];

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();
    }

    public static function scopeOrdered(): array
    {
        return static::orderBy('sort_order', 'ASC')->orderBy('id', 'DESC')->get();
    }
}
