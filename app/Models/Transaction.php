<?php
namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected static string $table = 'transactions';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'order_id', 'type', 'amount', 'fee',
        'balance_before', 'balance_after', 'reference',
        'description', 'status'
    ];

    public function user(\stdClass $transaction): ?\stdClass
    {
        return User::find($transaction->user_id);
    }

    public function order(\stdClass $transaction): ?\stdClass
    {
        return Order::find($transaction->order_id);
    }

    public static function scopeByType(string $type): array
    {
        return static::where('type', $type)->orderBy('id', 'DESC')->get();
    }
}
