<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Setting extends Model
{
    protected static string $table = 'settings';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'key', 'value', 'group', 'type', 'description'
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        $existing = static::where('key', $key)->first();
        if ($existing) {
            static::update($existing->id, ['value' => $value]);
        } else {
            static::create(['key' => $key, 'value' => $value]);
        }
    }

    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)->get();
    }
}
