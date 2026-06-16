<?php
namespace App\Models;

use App\Core\Model;

class Brand extends Model
{
    protected static string $table = 'brands';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name', 'slug', 'description', 'logo', 'is_active'
    ];

    public function products(\stdClass $brand): array
    {
        return Product::where('brand_id', $brand->id)->get();
    }

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->orderBy('name', 'ASC')->get();
    }
}
