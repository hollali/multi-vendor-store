<?php
namespace App\Models;

use App\Core\Model;

class Withdrawal extends Model
{
    protected static string $table = 'withdrawals';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'vendor_id', 'amount', 'fee', 'net_amount', 'bank_account_id',
        'payment_method', 'status', 'notes', 'processed_at', 'processed_by'
    ];

    public function vendor(\stdClass $withdrawal): ?\stdClass
    {
        return User::find($withdrawal->vendor_id);
    }

    public static function scopeByStatus(string $status): array
    {
        return static::where('status', $status)->orderBy('id', 'DESC')->get();
    }
}
