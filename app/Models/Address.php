<?php
namespace App\Models;

use App\Core\Model;

class Address extends Model
{
    protected static string $table = 'addresses';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'label', 'full_name', 'phone', 'street_address',
        'city', 'state', 'country', 'postal_code', 'is_default'
    ];

    public function user(\stdClass $address): ?\stdClass
    {
        return User::find($address->user_id);
    }

    public static function scopeByUser(int $userId): array
    {
        return static::where('user_id', $userId)->get();
    }

    public static function scopeDefault(): ?\stdClass
    {
        return static::where('is_default', 1)->first();
    }
}
