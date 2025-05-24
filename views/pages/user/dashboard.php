<?php
/**
 * User Dashboard View
 * File path: views/pages/user/dashboard.php
 */

$currentPage = 'dashboard';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Dashboard Header -->
    <div class="<?= $isRtl ? 'text-right' : 'text-left' ?> mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= $this->localization->t('user.welcome_back') ?>, <?= htmlspecialchars($user['name']) ?>!
        </h1>
        <p class="mt-2 text-gray-600">
            <?= $this->localization->t('user.dashboard_subtitle') ?>
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->hasFlash('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md" role="alert">
        <div class="flex">
            <i class="fas fa-check-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
            <span><?= $this->session->getFlash('success') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($this->session->hasFlash('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md" role="alert">
        <div class="flex">
            <i class="fas fa-exclamation-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
            <span><?= $this->session->getFlash('error') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Quote Requests -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= $this->localization->t('user.total_quote_requests') ?>
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                <?= number_format($stats['total_quote_requests']) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= $this->localization->t('user.total_orders') ?>
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                <?= number_format($stats['total_orders']) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Quotes -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= $this->localization->t('user.pending_quotes') ?>
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                <?= number_format($stats['pending_quotes']) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cog text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= $this->localization->t('user.active_orders') ?>
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                <?= number_format($stats['active_orders']) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow-sm rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
                <?= $this->localization->t('user.quick_actions') ?>
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="/services" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-search text-blue-600"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?>">
                        <h3 class="text-sm font-medium text-gray-900">
                            <?= $this->localization->t('user.browse_services') ?>
                        </h3>
                        <p class="text-sm text-gray-500">
                            <?= $this->localization->t('user.find_printing_services') ?>
                        </p>
                    </div>
                </a>

                <a href="/vendors" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-green-600"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?>">
                        <h3 class="text-sm font-medium text-gray-900">
                            <?= $this->localization->t('user.browse_vendors') ?>
                        </h3>
                        <p class="text-sm text-gray-500">
                            <?= $this->localization->t('user.explore_print_shops') ?>
                        </p>
                    </div>
                </a>

                <a href="/quotes/history" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-history text-purple-600"></i>
                        </div>
                    </div>
                    <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?>">
                        <h3 class="text-sm font-medium text-gray-900">
                            <?= $this->localization->t('user.view_history') ?>
                        </h3>
                        <p class="text-sm text-gray-500">
                            <?= $this->localization->t('user.track_requests') ?>
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Activity Chart -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <?= $this->localization->t('user.activity_overview') ?>
                </h2>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <?= $this->localization->t('user.recent_notifications') ?>
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (!empty($notifications)): ?>
                    <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                    <div class="p-4 <?= $notification['read_at'] ? 'bg-white' : 'bg-blue-50' ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-bell text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="<?= $isRtl ? 'mr-3' : 'ml-3' ?> flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($notification["title_$currentLanguage"]) ?>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?= htmlspecialchars($notification["message_$currentLanguage"]) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-2">
                                    <?= date('M j, Y \a\t g:i A', strtotime($notification['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="p-4 text-center">
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                            <?= $this->localization->t('user.view_all_notifications') ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-6 text-center">
                        <i class="fas fa-bell-slash text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">
                            <?= $this->localization->t('user.no_notifications') ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Quote Requests and Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Quote Requests -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">
                    <?= $this->localization->t('user.recent_quote_requests') ?>
                </h2>
                <a href="/quotes/history" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                    <?= $this->localization->t('user.view_all') ?>
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (!empty($recentQuotes)): ?>
                    <?php foreach ($recentQuotes as $quote): ?>
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($quote["service_title_$currentLanguage"]) ?>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?= htmlspecialchars($quote["vendor_name_$currentLanguage"]) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= date('M j, Y', strtotime($quote['created_at'])) ?>
                                </p>
                            </div>
                            <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $quote['status'] === 'completed' ? 'bg-green-100 text-green-800' : ($quote['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                                    <?= $this->localization->t("quotes.status_{$quote['status']}") ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/quotes/track/<?= $quote['tracking_id'] ?>" class="text-sm text-blue-600 hover:text-blue-500">
                                <?= $this->localization->t('user.view_details') ?> →
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-6 text-center">
                        <i class="fas fa-file-alt text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">
                            <?= $this->localization->t('user.no_quote_requests') ?>
                        </p>
                        <a href="/services" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-500">
                            <?= $this->localization->t('user.browse_services_now') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">
                    <?= $this->localization->t('user.recent_orders') ?>
                </h2>
                <a href="/orders/history" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                    <?= $this->localization->t('user.view_all') ?>
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (!empty($recentOrders)): ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <?= $this->localization->t('user.order') ?> #<?= $order['id'] ?>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?= htmlspecialchars($order["service_title_$currentLanguage"]) ?>
                                </p>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= date('M j, Y', strtotime($order['created_at'])) ?>
                                </p>
                            </div>
                            <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') ?>">
                                    <?= $this->localization->t("orders.status_{$order['status']}") ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/orders/<?= $order['id'] ?>" class="text-sm text-blue-600 hover:text-blue-500">
                                <?= $this->localization->t('user.view_order') ?> →
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-6 text-center">
                        <i class="fas fa-shopping-bag text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">
                            <?= $this->localization->t('user.no_orders') ?>
                        </p>
                        <a href="/services" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-500">
                            <?= $this->localization->t('user.start_shopping') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data -->
<script type="text/javascript">
    window.chartData = {
        months: <?= json_encode($monthlyData['months']) ?>,
        quotes: <?= json_encode($monthlyData['quotes']) ?>,
        orders: <?= json_encode($monthlyData['orders']) ?>
    };
</script>
