<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected static string $table = 'payments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'order_id', 'payment_method', 'payment_reference',
        'amount', 'currency', 'status', 'paid_at', 'notes'
    ];

    public function order(\stdClass $payment): ?\stdClass
    {
        return Order::find($payment->order_id);
    }

    public static function scopeByReference(string $reference): ?\stdClass
    {
        return static::where('payment_reference', $reference)->first();
    }
}
