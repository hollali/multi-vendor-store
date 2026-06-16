<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'notifications'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= $unreadCount ?? 0 ?> unread notification(s)</p>
            </div>
            <?php if (($unreadCount ?? 0) > 0): ?>
                <form action="/notifications/mark-all-read" method="POST" class="mt-3 sm:mt-0">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-check-double"></i> Mark All as Read</button>
                </form>
            <?php endif; ?>
        </div>

        <?php $notifications = $notifications ?? []; ?>
        <?php if (empty($notifications)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                    <i class="fas fa-bell text-indigo-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">All caught up!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">No notifications at the moment.</p>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($notifications as $notif): ?>
                        <?php
                        $isRead = $notif->is_read ?? $notif['is_read'] ?? false;
                        $icon = $notif->icon ?? $notif['icon'] ?? 'fa-bell';
                        $iconBg = $notif->icon_bg ?? $notif['icon_bg'] ?? ($isRead ? 'bg-gray-100 dark:bg-gray-700' : 'bg-indigo-100 dark:bg-indigo-900/30');
                        $iconColor = $notif->icon_color ?? $notif['icon_color'] ?? ($isRead ? 'text-gray-400' : 'text-indigo-600 dark:text-indigo-400');
                        ?>
                        <div class="px-5 py-4 flex items-start gap-4 <?= $isRead ? '' : 'bg-indigo-50/50 dark:bg-indigo-900/10' ?> hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <span class="w-10 h-10 rounded-full <?= $iconBg ?> flex items-center justify-center <?= $iconColor ?> flex-shrink-0">
                                <i class="fas <?= $icon ?>"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white <?= $isRead ? '' : 'text-gray-900 dark:text-white' ?>"><?= htmlspecialchars($notif->title ?? $notif['title'] ?? '') ?></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($notif->message ?? $notif['message'] ?? '') ?></p>
                                    </div>
                                    <?php if (!$isRead): ?>
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0 mt-2"></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= htmlspecialchars(timeAgo($notif->created_at ?? $notif['created_at'] ?? '')) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function timeAgo($datetime) {
    if (!$datetime) return '';
    $ts = strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' day ago';
    return date('M d, Y', $ts);
}
?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
