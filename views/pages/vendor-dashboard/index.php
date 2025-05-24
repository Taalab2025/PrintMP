<?php
/**
 * File path: views/pages/vendor-dashboard/index.php
 * Vendor Dashboard Home
 *
 * This is the main dashboard page for vendors, showing overview statistics,
 * recent activity, and quick actions.
 */

// Get language and RTL status
$isRtl = $this->localization->isRtl();
$directionClass = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
?>

<!-- Welcome section -->
<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4"><?= $this->localization->t('vendor.welcome_back') ?>, <?= htmlspecialchars($vendor["company_name_{$this->localization->getCurrentLanguage()}"]) ?></h2>
    <p class="text-gray-600"><?= $this->localization->t('vendor.dashboard_intro') ?></p>

    <?php if (isset($quotesLeft) && $quotesLeft !== null): ?>
    <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded">
        <p>
            <i class="fas fa-info-circle <?= $marginEnd ?>-2"></i>
            <?= $this->localization->t('vendor.quotes_left', ['count' => $quotesLeft]) ?>
            <?php if ($quotesLeft <= 3): ?>
            <a href="/vendor/subscription" class="text-blue-800 font-semibold underline"><?= $this->localization->t('vendor.upgrade_now') ?></a>
            <?php endif; ?>
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Stats cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Pending Quotes -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="rounded-full p-3 bg-yellow-100 text-yellow-600 <?= $marginEnd ?>-4">
            <i class="fas fa-file-invoice-dollar text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500"><?= $this->localization->t('vendor.pending_quotes') ?></p>
            <p class="text-2xl font-bold"><?= $stats['pending_quotes'] ?></p>
            <a href="/vendor/quotes?status=pending" class="text-sm text-blue-600 hover:underline"><?= $this->localization->t('vendor.view_all') ?></a>
        </div>
    </div>

    <!-- Services Count -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="rounded-full p-3 bg-blue-100 text-blue-600 <?= $marginEnd ?>-4">
            <i class="fas fa-print text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500"><?= $this->localization->t('vendor.active_services') ?></p>
            <p class="text-2xl font-bold"><?= $stats['services_count'] ?></p>
            <a href="/vendor/services" class="text-sm text-blue-600 hover:underline"><?= $this->localization->t('vendor.manage_services') ?></a>
        </div>
    </div>

    <!-- Quotes This Month -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="rounded-full p-3 bg-green-100 text-green-600 <?= $marginEnd ?>-4">
            <i class="fas fa-calculator text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500"><?= $this->localization->t('vendor.quotes_this_month') ?></p>
            <p class="text-2xl font-bold"><?= $stats['quotes_this_month'] ?></p>
            <a href="/vendor/analytics" class="text-sm text-blue-600 hover:underline"><?= $this->localization->t('vendor.view_analytics') ?></a>
        </div>
    </div>

    <!-- Conversion Rate -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="rounded-full p-3 bg-purple-100 text-purple-600 <?= $marginEnd ?>-4">
            <i class="fas fa-chart-line text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500"><?= $this->localization->t('vendor.conversion_rate') ?></p>
            <p class="text-2xl font-bold"><?= number_format($stats['conversion_rate'] * 100, 1) ?>%</p>
            <a href="/vendor/analytics" class="text-sm text-blue-600 hover:underline"><?= $this->localization->t('vendor.view_analytics') ?></a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Quote Requests -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><?= $this->localization->t('vendor.recent_quote_requests') ?></h3>
            <a href="/vendor/quotes" class="text-sm text-blue-600 hover:underline"><?= $this->localization->t('vendor.view_all') ?></a>
        </div>

        <?php if (empty($stats['recent_quote_requests'])): ?>
        <div class="text-center py-6 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p><?= $this->localization->t('vendor.no_recent_quotes') ?></p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-2"><?= $this->localization->t('vendor.service') ?></th>
                        <th class="text-left py-3 px-2"><?= $this->localization->t('vendor.request_date') ?></th>
                        <th class="text-left py-3 px-2"><?= $this->localization->t('vendor.status') ?></th>
                        <th class="text-left py-3 px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_quote_requests'] as $request): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-2">
                            <div class="font-medium"><?= htmlspecialchars($request['service_title']) ?></div>
                            <div class="text-sm text-gray-500">#<?= $request['id'] ?></div>
                        </td>
                        <td class="py-3 px-2"><?= date('M d, Y', strtotime($request['created_at'])) ?></td>
                        <td class="py-3 px-2">
                            <?php
                            $statusClass = '';
                            switch ($request['status']) {
                                case 'pending':
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'quoted':
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'accepted':
                                    $statusClass = 'bg-green-100 text-green-800';
                                    break;
                                case 'rejected':
                                    $statusClass = 'bg-red-100 text-red-800';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800';
                            }
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs <?= $statusClass ?>">
                                <?= $this->localization->t("vendor.status_{$request['status']}") ?>
                            </span>
                        </td>
                        <td class="py-3 px-2">
                            <a href="/vendor/quotes/<?= $request['id'] ?>" class="text-blue-600 hover:underline"><?= $this->localization->t('vendor.view') ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Top Services -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><?= $this->localization->t('vendor.top_services') ?></h3>
            <a href="/vendor/services" class="text-sm text-blue-600 hover:underline"><?= $this->localization->t('vendor.manage_services') ?></a>
        </div>

        <?php if (empty($stats['top_services'])): ?>
        <div class="text-center py-6 text-gray-500">
            <i class="fas fa-print text-4xl mb-3"></i>
            <p><?= $this->localization->t('vendor.no_services') ?></p>
            <a href="/vendor/services/add" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <?= $this->localization->t('vendor.add_service') ?>
            </a>
        </div>
        <?php else: ?>
        <div>
            <?php foreach ($stats['top_services'] as $index => $service): ?>
            <div class="p-4 <?= $index < count($stats['top_services']) - 1 ? 'border-b' : '' ?> hover:bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium"><?= htmlspecialchars($service['title']) ?></h4>
                        <div class="text-sm text-gray-500"><?= $this->localization->t('vendor.requests') ?>: <?= $service['request_count'] ?></div>
                    </div>
                    <div class="flex items-center">
                        <?php if ($service['status'] === 'active'): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs"><?= $this->localization->t('vendor.active') ?></span>
                        <?php else: ?>
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs"><?= $this->localization->t('vendor.inactive') ?></span>
                        <?php endif; ?>
                        <a href="/vendor/services/edit/<?= $service['id'] ?>" class="<?= $marginStart ?>-2 text-blue-600 hover:underline"><?= $this->localization->t('vendor.edit') ?></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white p-6 rounded-lg shadow-md mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4"><?= $this->localization->t('vendor.quick_actions') ?></h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <a href="/vendor/services/add" class="p-4 border border-gray-200 rounded-lg flex flex-col items-center text-center hover:bg-blue-50 hover:border-blue-200 transition-colors">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-plus-circle text-lg"></i>
            </div>
            <span class="font-medium"><?= $this->localization->t('vendor.add_service') ?></span>
            <span class="text-sm text-gray-500"><?= $this->localization->t('vendor.add_service_desc') ?></span>
        </a>

        <a href="/vendor/quotes?status=pending" class="p-4 border border-gray-200 rounded-lg flex flex-col items-center text-center hover:bg-blue-50 hover:border-blue-200 transition-colors">
            <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
            </div>
            <span class="font-medium"><?= $this->localization->t('vendor.manage_quotes') ?></span>
            <span class="text-sm text-gray-500"><?= $this->localization->t('vendor.manage_quotes_desc') ?></span>
        </a>

        <a href="/vendor/analytics" class="p-4 border border-gray-200 rounded-lg flex flex-col items-center text-center hover:bg-blue-50 hover:border-blue-200 transition-colors">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-chart-bar text-lg"></i>
            </div>
            <span class="font-medium"><?= $this->localization->t('vendor.analytics') ?></span>
            <span class="text-sm text-gray-500"><?= $this->localization->t('vendor.analytics_desc') ?></span>
        </a>

        <a href="/vendor/profile" class="p-4 border border-gray-200 rounded-lg flex flex-col items-center text-center hover:bg-blue-50 hover:border-blue-200 transition-colors">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-user-cog text-lg"></i>
            </div>
            <span class="font-medium"><?= $this->localization->t('vendor.update_profile') ?></span>
            <span class="text-sm text-gray-500"><?= $this->localization->t('vendor.update_profile_desc') ?></span>
        </a>
    </div>
</div>
