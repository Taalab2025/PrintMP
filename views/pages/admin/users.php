<?php
/**
 * Admin Users Management
 * File path: views/pages/admin/users.php
 */

$pageTitle = $localization->t('admin.users');
ob_start();
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                <?= $localization->t('admin.users') ?>
            </h1>
            <p class="mt-2 text-gray-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                <?= $localization->t('admin.manage_users_subtitle') ?>
            </p>
        </div>

        <div class="flex items-center space-x-3 <?= $isRtl ? 'space-x-reverse' : '' ?>">
            <span class="text-sm text-gray-500">
                <?= number_format($totalUsers) ?> <?= $localization->t('admin.total_users') ?>
            </span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-4">
        <!-- Search -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                <?= $localization->t('admin.search') ?>
            </label>
            <input type="text" name="q" value="<?= htmlspecialchars($filters['search_term'] ?? '') ?>"
                   placeholder="<?= $localization->t('admin.search_users') ?>"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 <?= $isRtl ? 'text-right' : 'text-left' ?>">
        </div>

        <!-- Role Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                <?= $localization->t('admin.role') ?>
            </label>
            <select name="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value=""><?= $localization->t('admin.all_roles') ?></option>
                <option value="customer" <?= ($filters['role'] ?? '') === 'customer' ? 'selected' : '' ?>>
                    <?= $localization->t('admin.customer') ?>
                </option>
                <option value="vendor" <?= ($filters['role'] ?? '') === 'vendor' ? 'selected' : '' ?>>
                    <?= $localization->t('admin.vendor') ?>
                </option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                    <?= $localization->t('admin.administrator') ?>
                </option>
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                <?= $localization->t('admin.status') ?>
            </label>
            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value=""><?= $localization->t('admin.all_statuses') ?></option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>
                    <?= $localization->t('admin.active') ?>
                </option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>
                    <?= $localization->t('admin.inactive') ?>
                </option>
            </select>
        </div>

        <!-- Actions -->
        <div class="flex items-end space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                <?= $localization->t('admin.filter') ?>
            </button>
            <a href="/admin/users" class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                <?= $localization->t('admin.clear') ?>
            </a>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <?php if (!empty($users)): ?>
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $localization->t('admin.user') ?>
                        </th>
                        <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $localization->t('admin.role') ?>
                        </th>
                        <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $localization->t('admin.joined') ?>
                        </th>
                        <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $localization->t('admin.status') ?>
                        </th>
                        <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $localization->t('admin.actions') ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center <?= $isRtl ? 'ml-4' : 'mr-4' ?>">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($user['name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php
                                    switch ($user['role']) {
                                        case 'admin':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        case 'vendor':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'customer':
                                        default:
                                            echo 'bg-green-100 text-green-800';
                                    }
                                    ?>">
                                    <?= $localization->t('admin.' . $user['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?= date('M j, Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $localization->t('admin.status_' . $user['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                                    <?php if ($user['status'] === 'active'): ?>
                                        <form method="POST" class="inline" onsubmit="return confirm('<?= $localization->t('admin.confirm_deactivate') ?>')">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="action" value="deactivate">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <?= $localization->t('admin.deactivate') ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="action" value="activate">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                <?= $localization->t('admin.activate') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <form method="POST" class="inline" onsubmit="return confirm('<?= $localization->t('admin.confirm_delete') ?>')">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <?= $localization->t('admin.delete') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden">
            <?php foreach ($users as $user): ?>
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-start space-x-3 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($user['name']) ?>
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $localization->t('admin.status_' . $user['status']) ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">
                                <?= htmlspecialchars($user['email']) ?>
                            </p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php
                                    switch ($user['role']) {
                                        case 'admin':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        case 'vendor':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'customer':
                                        default:
                                            echo 'bg-green-100 text-green-800';
                                    }
                                    ?>">
                                    <?= $localization->t('admin.' . $user['role']) ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                </span>
                            </div>
                            <div class="mt-3 flex items-center space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                                <?php if ($user['status'] === 'active'): ?>
                                    <form method="POST" class="inline" onsubmit="return confirm('<?= $localization->t('admin.confirm_deactivate') ?>')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="action" value="deactivate">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-900">
                                            <?= $localization->t('admin.deactivate') ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="text-xs text-green-600 hover:text-green-900">
                                            <?= $localization->t('admin.activate') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($user['role'] !== 'admin'): ?>
                                    <form method="POST" class="inline" onsubmit="return confirm('<?= $localization->t('admin.confirm_delete') ?>')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-900">
                                            <?= $localization->t('admin.delete') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($currentPage > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>"
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <?= $localization->t('admin.previous') ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>"
                               class="<?= $isRtl ? 'mr-3' : 'ml-3' ?> relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <?= $localization->t('admin.next') ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                <?= $localization->t('admin.showing') ?>
                                <span class="font-medium"><?= (($currentPage - 1) * 20) + 1 ?></span>
                                <?= $localization->t('admin.to') ?>
                                <span class="font-medium"><?= min($currentPage * 20, $totalUsers) ?></span>
                                <?= $localization->t('admin.of') ?>
                                <span class="font-medium"><?= $totalUsers ?></span>
                                <?= $localization->t('admin.results') ?>
                            </p>
                        </div>

                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px <?= $isRtl ? 'space-x-reverse' : '' ?>" aria-label="Pagination">
                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                                       class="relative inline-flex items-center px-4 py-2 border text-sm font-medium
                                           <?= $i === $currentPage
                                               ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                               : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                <?= $localization->t('admin.no_users_found') ?>
            </h3>
            <p class="text-gray-500 mb-6">
                <?= $localization->t('admin.no_users_description') ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
