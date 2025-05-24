<?php
/**
 * Order History View
 * File path: views/pages/orders/history.php
 * Session: 7 - Quote Comparison & Order Placement
 */

$currentPage = 'orders';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="<?= $isRtl ? 'text-right' : 'text-left' ?> mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= $this->localization->t('orders.order_history') ?>
        </h1>
        <p class="mt-2 text-gray-600">
            <?= $this->localization->t('orders.order_history_subtitle') ?>
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

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="status" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= $this->localization->t('orders.filter_by_status') ?>
                </label>
                <select name="status"
                        id="status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value=""><?= $this->localization->t('orders.all_statuses') ?></option>
                    <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_pending') ?>
                    </option>
                    <option value="confirmed" <?= $selectedStatus === 'confirmed' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_confirmed') ?>
                    </option>
                    <option value="processing" <?= $selectedStatus === 'processing' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_processing') ?>
                    </option>
                    <option value="shipped" <?= $selectedStatus === 'shipped' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_shipped') ?>
                    </option>
                    <option value="delivered" <?= $selectedStatus === 'delivered' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_delivered') ?>
                    </option>
                    <option value="completed" <?= $selectedStatus === 'completed' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_completed') ?>
                    </option>
                    <option value="cancelled" <?= $selectedStatus === 'cancelled' ? 'selected' : '' ?>>
                        <?= $this->localization->t('orders.status_cancelled') ?>
                    </option>
                </select>
            </div>

            <div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-filter <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                    <?= $this->localization->t('general.filter') ?>
                </button>
            </div>
        </form>
    </div>

    <?php if (empty($orders)): ?>
        <!-- Empty State -->
        <div class="bg-white shadow-sm rounded-lg p-12 text-center">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-shopping-bag text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                <?= $this->localization->t('orders.no_orders_found') ?>
            </h3>
            <p class="text-gray-600 mb-6">
                <?= $this->localization->t('orders.no_orders_description') ?>
            </p>
            <a href="/services"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-search <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                <?= $this->localization->t('orders.browse_services') ?>
            </a>
        </div>
    <?php else: ?>
        <!-- Orders List - Desktop -->
        <div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('orders.order_id') ?>
                        </th>
                        <th scope="col" class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('orders.service') ?>
                        </th>
                        <th scope="col" class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('orders.vendor') ?>
                        </th>
                        <th scope="col" class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('orders.amount') ?>
                        </th>
                        <th scope="col" class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('orders.status') ?>
                        </th>
                        <th scope="col" class="px-6 py-3 <?= $isRtl ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('orders.date') ?>
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only"><?= $this->localization->t('general.actions') ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #<?= $order['id'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?= htmlspecialchars($order["service_title_$currentLanguage"]) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?= htmlspecialchars($order["vendor_name_$currentLanguage"]) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-purple-100 text-purple-800',
                                'shipped' => 'bg-indigo-100 text-indigo-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ];
                            $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                <?= $this->localization->t("orders.status_{$order['status']}") ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('M j, Y', strtotime($order['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap <?= $isRtl ? 'text-left' : 'text-right' ?> text-sm font-medium">
                            <a href="/orders/<?= $order['id'] ?>"
                               class="text-blue-600 hover:text-blue-900">
                                <?= $this->localization->t('orders.view_details') ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Orders List - Mobile -->
        <div class="md:hidden space-y-4">
            <?php foreach ($orders as $order): ?>
            <div class="bg-white shadow-sm rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-base font-medium text-gray-900">
                            <?= $this->localization->t('orders.order') ?> #<?= $order['id'] ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?= date('M j, Y', strtotime($order['created_at'])) ?>
                        </p>
                    </div>

                    <?php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'processing' => 'bg-purple-100 text-purple-800',
                        'shipped' => 'bg-indigo-100 text-indigo-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                        <?= $this->localization->t("orders.status_{$order['status']}") ?>
                    </span>
                </div>

                <div class="space-y-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($order["service_title_$currentLanguage"]) ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <?= htmlspecialchars($order["vendor_name_$currentLanguage"]) ?>
                        </p>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-base font-medium text-gray-900">
                            <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                        </span>

                        <a href="/orders/<?= $order['id'] ?>"
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                            <?= $this->localization->t('orders.view_details') ?>
                            <i class="fas fa-arrow-right <?= $isRtl ? 'mr-1 fa-flip-horizontal' : 'ml-1' ?>"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg mt-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?><?= $selectedStatus ? '&status=' . $selectedStatus : '' ?>"
                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <?= $this->localization->t('general.previous') ?>
                </a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?><?= $selectedStatus ? '&status=' . $selectedStatus : '' ?>"
                   class="<?= $currentPage > 1 ? '' : 'ml-3' ?> relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <?= $this->localization->t('general.next') ?>
                </a>
                <?php endif; ?>
            </div>

            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        <?= $this->localization->t('general.showing') ?>
                        <span class="font-medium"><?= (($currentPage - 1) * 10) + 1 ?></span>
                        <?= $this->localization->t('general.to') ?>
                        <span class="font-medium"><?= min($currentPage * 10, count($orders)) ?></span>
                        <?= $this->localization->t('general.of') ?>
                        <span class="font-medium"><?= count($orders) ?></span>
                        <?= $this->localization->t('general.results') ?>
                    </p>
                </div>

                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?><?= $selectedStatus ? '&status=' . $selectedStatus : '' ?>"
                           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-left <?= $isRtl ? 'fa-flip-horizontal' : '' ?>"></i>
                        </a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a href="?page=<?= $i ?><?= $selectedStatus ? '&status=' . $selectedStatus : '' ?>"
                           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?><?= $selectedStatus ? '&status=' . $selectedStatus : '' ?>"
                           class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-right <?= $isRtl ? 'fa-flip-horizontal' : '' ?>"></i>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
