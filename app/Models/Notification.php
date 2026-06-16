<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Notification extends Model
{
    protected static string $table = 'notifications';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'user_id', 'type', 'title', 'message', 'data',
        'is_read', 'read_at', 'link'
    ];

    public static function scopeUnread(int $userId): array
    {
        return static::where('user_id', $userId)->where('is_read', 0)->orderBy('id', 'DESC')->get();
    }

    public static function markAsRead(int $notificationId): void
    {
        static::update($notificationId, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function createForUser(int $userId, string $type, string $title, string $message, ?array $data = null, ?string $link = null): int
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'is_read' => 0,
            'link' => $link,
        ]);
    }
}
