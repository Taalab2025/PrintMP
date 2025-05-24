<?php
/**
 * Admin Dashboard Index
 * File path: views/pages/admin/index.php
 */

$pageTitle = $localization->t('admin.dashboard');
ob_start();
?>

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 <?= $isRtl ? 'text-right' : 'text-left' ?>">
        <?= $localization->t('admin.dashboard') ?>
    </h1>
    <p class="mt-2 text-gray-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
        <?= $localization->t('admin.dashboard_subtitle') ?>
    </p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                <p class="text-sm font-medium text-gray-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= $localization->t('admin.total_users') ?>
                </p>
                <p class="text-2xl font-bold text-gray-900 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= number_format($stats['total_users']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Total Vendors -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                <p class="text-sm font-medium text-gray-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= $localization->t('admin.total_vendors') ?>
                </p>
                <p class="text-2xl font-bold text-gray-900 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= number_format($stats['total_vendors']) ?>
                </p>
                <?php if ($stats['pending_vendors'] > 0): ?>
                    <p class="text-xs text-orange-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                        <?= $stats['pending_vendors'] ?> <?= $localization->t('admin.pending_approval') ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Total Services -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                <p class="text-sm font-medium text-gray-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= $localization->t('admin.total_services') ?>
                </p>
                <p class="text-2xl font-bold text-gray-900 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= number_format($stats['total_services']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                </div>
            </div>
            <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                <p class="text-sm font-medium text-gray-600 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= $localization->t('admin.total_orders') ?>
                </p>
                <p class="text-2xl font-bold text-gray-900 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= number_format($stats['total_orders']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Monthly Activity Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 <?= $isRtl ? 'text-right' : 'text-left' ?>">
            <?= $localization->t('admin.monthly_activity') ?>
        </h3>
        <div class="relative h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Quote Requests Overview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 <?= $isRtl ? 'text-right' : 'text-left' ?>">
            <?= $localization->t('admin.quote_requests') ?>
        </h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600"><?= $localization->t('admin.total_requests') ?></span>
                <span class="text-lg font-semibold text-gray-900"><?= number_format($stats['total_quote_requests']) ?></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
            </div>
            <p class="text-xs text-gray-500 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                <?= $localization->t('admin.quote_progress_text') ?>
            </p>
        </div>
    </div>
</div>

<!-- Recent Activity Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <?= $localization->t('admin.recent_orders') ?>
                </h3>
                <a href="/admin/orders" class="text-sm text-blue-600 hover:text-blue-800">
                    <?= $localization->t('admin.view_all') ?>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <?php if (!empty($stats['recent_orders'])): ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.order_id') ?>
                            </th>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.customer') ?>
                            </th>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.amount') ?>
                            </th>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.status') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($stats['recent_orders'] as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?= $order['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= htmlspecialchars($order['contact_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= number_format($order['total_amount'], 2) ?> <?= $localization->t('general.currency') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch ($order['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'processing':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'completed':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= $localization->t('orders.status_' . $order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                    <p><?= $localization->t('admin.no_recent_orders') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Vendors -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <?= $localization->t('admin.recent_vendors') ?>
                </h3>
                <a href="/admin/vendors" class="text-sm text-blue-600 hover:text-blue-800">
                    <?= $localization->t('admin.view_all') ?>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <?php if (!empty($stats['recent_vendors'])): ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.company') ?>
                            </th>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.joined') ?>
                            </th>
                            <th class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $localization->t('admin.status') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($stats['recent_vendors'] as $vendor): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center <?= $isRtl ? 'ml-3' : 'mr-3' ?>">
                                            <i class="fas fa-store text-gray-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($vendor["company_name_{$currentLanguage}"] ?? $vendor['company_name_en']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= date('M j, Y', strtotime($vendor['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch ($vendor['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'active':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'suspended':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= $localization->t('admin.status_' . $vendor['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-store text-4xl mb-2"></i>
                    <p><?= $localization->t('admin.no_recent_vendors') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Activity Chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');

    // Process monthly stats data
    const monthlyData = <?= json_encode($stats['monthly_stats']) ?
    >;

    // Group data by type
    const userData = {};
    const orderData = {};

    monthlyData.forEach(item => {
        if (item.type === 'users') {
            userData[item.month] = item.count;
        } else if (item.type === 'orders') {
            orderData[item.month] = item.count;
        }
    });

    // Get last 6 months
    const months = [];
    const userCounts = [];
    const orderCounts = [];

    for (let i = 5; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const monthKey = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
        const monthLabel = date.toLocaleDateString('<?= $currentLanguage === 'ar' ? 'ar-EG' : 'en-US' ?>', { month: 'short', year: 'numeric' });

        months.push(monthLabel);
        userCounts.push(userData[monthKey] || 0);
        orderCounts.push(orderData[monthKey] || 0);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: '<?= $localization->t('admin.new_users') ?>',
                data: userCounts,
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.4,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                pointRadius: 4
            }, {
                label: '<?= $localization->t('admin.new_orders') ?>',
                data: orderCounts,
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                tension: 0.4,
                pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: '<?= $isRtl ? 'right' : 'left' ?>'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
