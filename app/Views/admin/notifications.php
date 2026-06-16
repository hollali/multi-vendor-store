<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'notifications'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Send notifications to platform users.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><i class="fas fa-paper-plane text-primary-500 mr-2"></i>Send Notification</h2>
                <form action="/admin/notifications/send" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Send To</label>
                            <div class="space-y-2">
                                <?php $selectedRecipients = $_POST['recipients'] ?? ['all_users']; ?>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                                    <input type="radio" name="recipients" value="all_users" <?= in_array('all_users', (array)$selectedRecipients) || $selectedRecipients === 'all_users' ? 'checked' : '' ?> class="text-primary-700 focus:ring-primary-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">All Users</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Customers and vendors</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                                    <input type="radio" name="recipients" value="all_customers" <?= $selectedRecipients === 'all_customers' ? 'checked' : '' ?> class="text-primary-700 focus:ring-primary-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">All Customers</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Registered customers only</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                                    <input type="radio" name="recipients" value="all_vendors" <?= $selectedRecipients === 'all_vendors' ? 'checked' : '' ?> class="text-primary-700 focus:ring-primary-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">All Vendors</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">All registered vendors</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                            <input type="text" name="title" required class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="Notification title">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                            <textarea name="message" required rows="4" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="Write your notification message..."></textarea>
                        </div>

                        <button type="submit" class="w-full px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                            <i class="fas fa-paper-plane mr-1.5"></i> Send Notification
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white"><i class="fas fa-history text-gray-500 mr-2"></i>Sent Notifications</h2>
                </div>
                <?php $sentNotifications = $sentNotifications ?? $notifications ?? []; ?>
                <?php if (empty($sentNotifications)): ?>
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-bell text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No notifications sent</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Your sent notifications will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                    <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Recipients</th>
                                    <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Title</th>
                                    <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Message</th>
                                    <th class="px-5 py-3 font-semibold text-gray-600 dark:text-gray-400">Sent</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php foreach ($sentNotifications as $n): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $n->recipients ?? $n['recipients'] ?? ''))) ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($n->title ?? $n['title'] ?? '') ?></td>
                                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400 truncate max-w-[250px]"><?= htmlspecialchars($n->message ?? $n['message'] ?? '') ?></td>
                                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap"><?= htmlspecialchars(date('M d, Y h:i A', strtotime($n->created_at ?? $n['created_at'] ?? ''))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>