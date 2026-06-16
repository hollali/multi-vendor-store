<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Category extends Model
{
    protected static string $table = 'categories';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'parent_id', 'name', 'slug', 'description', 'image',
        'icon', 'sort_order', 'is_active'
    ];

    public function parent(\stdClass $category): ?\stdClass
    {
        if (empty($category->parent_id)) {
            return null;
        }
        return static::find($category->parent_id);
    }

    public function children(\stdClass $category): array
    {
        return static::where('parent_id', $category->id)->orderBy('sort_order', 'ASC')->get();
    }

    public function products(\stdClass $category): array
    {
        return Product::where('category_id', $category->id)->get();
    }

    public function getBreadcrumbs(\stdClass $category): array
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = ($current->parent_id ?? null) ? static::find($current->parent_id) : null;
        }

        return $breadcrumbs;
    }

    public static function scopeActive(): array
    {
        return static::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();
    }

    public static function scopeParents(): array
    {
        return static::where('parent_id', null, 'IS')->where('is_active', 1)->orderBy('sort_order', 'ASC')->get();
    }
}
