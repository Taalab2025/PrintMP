<?php
/**
 * File path: views/pages/vendor-dashboard/quotes.php
 * Vendor Quote Requests Page
 *
 * This page displays all quote requests for the vendor with filters and actions.
 */

// Get language and RTL status
$isRtl = $this->localization->isRtl();
$directionClass = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800"><?= $this->localization->t('vendor.quote_requests') ?></h1>
        <p class="text-gray-600 mt-1"><?= $this->localization->t('vendor.quote_requests_desc') ?></p>
    </div>
</div>

<?php if (isset($quotesLeft) && $quotesLeft !== null): ?>
<div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="<?= $marginStart ?>-3">
            <p>
                <?= $this->localization->t('vendor.quotes_left', ['count' => $quotesLeft]) ?>
                <?php if ($quotesLeft <= 3): ?>
                <a href="/vendor/subscription" class="text-blue-800 font-semibold underline"><?= $this->localization->t('vendor.upgrade_now') ?></a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <form action="/vendor/quotes" method="GET" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1"><?= $this->localization->t('general.search') ?></label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="<?= $this->localization->t('vendor.search_quotes_placeholder') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="w-full md:w-48">
            <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1"><?= $this->localization->t('vendor.service') ?></label>
            <select id="service_id" name="service_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value=""><?= $this->localization->t('general.all_services') ?></option>
                <?php foreach ($services as $service): ?>
                <option value="<?= $service['id'] ?>" <?= (isset($filters['service_id']) && $filters['service_id'] == $service['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-full md:w-48">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1"><?= $this->localization->t('general.status') ?></label>
            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value=""><?= $this->localization->t('general.all_statuses') ?></option>
                <option value="pending" <?= (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.status_pending') ?>
                </option>
                <option value="quoted" <?= (isset($filters['status']) && $filters['status'] === 'quoted') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.status_quoted') ?>
                </option>
                <option value="accepted" <?= (isset($filters['status']) && $filters['status'] === 'accepted') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.status_accepted') ?>
                </option>
                <option value="rejected" <?= (isset($filters['status']) && $filters['status'] === 'rejected') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.status_rejected') ?>
                </option>
                <option value="expired" <?= (isset($filters['status']) && $filters['status'] === 'expired') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.status_expired') ?>
                </option>
            </select>
        </div>

        <div>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-search <?= $marginEnd ?>-2"></i> <?= $this->localization->t('general.filter') ?>
            </button>
        </div>
    </form>
</div>

<!-- Quote requests list -->
<div class="bg-white rounded-lg shadow-md">
    <?php if (empty($quoteRequests)): ?>
    <div class="p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-file-invoice-dollar text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2"><?= $this->localization->t('vendor.no_quotes_found') ?></h3>
        <p class="text-gray-600"><?= $this->localization->t('vendor.no_quotes_found_desc') ?></p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.request_id') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.service') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.customer') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.date') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.status') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('general.actions') ?>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($quoteRequests as $request): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">#<?= $request['id'] ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($request['service_title']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if (!empty($request['user_id'])): ?>
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($request['user_name'] ?? $request['contact_name']) ?></div>
                        <div class="text-sm text-gray-500"><?= $request['user_id'] ? $this->localization->t('vendor.registered_user') : $this->localization->t('vendor.guest_user') ?></div>
                        <?php else: ?>
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($request['contact_name']) ?></div>
                        <div class="text-sm text-gray-500"><?= $this->localization->t('vendor.guest_user') ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= date('M d, Y', strtotime($request['created_at'])) ?></div>
                        <div class="text-sm text-gray-500"><?= date('H:i', strtotime($request['created_at'])) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                            case 'expired':
                                $statusClass = 'bg-gray-100 text-gray-800';
                                break;
                            default:
                                $statusClass = 'bg-gray-100 text-gray-800';
                        }
                        ?>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                            <?= $this->localization->t("vendor.status_{$request['status']}") ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="/vendor/quotes/<?= $request['id'] ?>" class="text-blue-600 hover:text-blue-900">
                            <?php if ($request['status'] === 'pending'): ?>
                            <span class="bg-yellow-50 text-yellow-800 px-2 py-1 rounded-md">
                                <?= $this->localization->t('vendor.respond_now') ?>
                            </span>
                            <?php else: ?>
                            <?= $this->localization->t('vendor.view_details') ?>
                            <?php endif; ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total'] > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-700">
                <?= $this->localization->t('general.showing_page', ['current' => $pagination['current'], 'total' => $pagination['total']]) ?>
            </div>
            <div class="flex space-x-2">
                <?php if ($pagination['current'] > 1): ?>
                <a href="?page=<?= $pagination['current'] - 1 ?><?= isset($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= isset($filters['service_id']) ? '&service_id=' . urlencode($filters['service_id']) : '' ?><?= isset($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                    <?= $this->localization->t('general.previous') ?>
                </a>
                <?php endif; ?>

                <?php if ($pagination['current'] < $pagination['total']): ?>
                <a href="?page=<?= $pagination['current'] + 1 ?><?= isset($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= isset($filters['service_id']) ? '&service_id=' . urlencode($filters['service_id']) : '' ?><?= isset($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                    <?= $this->localization->t('general.next') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
