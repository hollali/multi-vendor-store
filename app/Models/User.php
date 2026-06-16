<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'uuid', 'first_name', 'last_name', 'email', 'password', 'role', 'phone', 'avatar',
        'email_verified_at', 'remember_token', 'status', 'login_attempts', 'locked_until'
    ];

    public static function findByEmail(string $email): ?\stdClass
    {
        return static::findBy('email', $email);
    }

    public function getFullName(\stdClass $user): string
    {
        return trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    }

    public function isAdmin(\stdClass $user): bool
    {
        return ($user->role ?? '') === 'admin';
    }

    public function isVendor(\stdClass $user): bool
    {
        return ($user->role ?? '') === 'vendor';
    }

    public function isCustomer(\stdClass $user): bool
    {
        return ($user->role ?? '') === 'customer';
    }

    public function getStore(\stdClass $user): ?\stdClass
    {
        return Store::where('vendor_id', $user->id)->first();
    }

    public function getAddresses(\stdClass $user): array
    {
        return Address::where('user_id', $user->id)->get();
    }
}
