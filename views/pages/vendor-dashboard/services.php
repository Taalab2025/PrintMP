<?php
/**
 * File path: views/pages/vendor-dashboard/services.php
 * Vendor Services Management Page
 *
 * This page allows vendors to view, add, edit and manage their services.
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
        <h1 class="text-2xl font-semibold text-gray-800"><?= $this->localization->t('vendor.services') ?></h1>
        <p class="text-gray-600 mt-1"><?= $this->localization->t('vendor.services_desc') ?></p>
    </div>
    <a href="/vendor/services/add" class="mt-4 sm:mt-0 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
        <i class="fas fa-plus <?= $marginEnd ?>-2"></i> <?= $this->localization->t('vendor.add_service') ?>
    </a>
</div>

<!-- Filters and search -->
<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <form action="/vendor/services" method="GET" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1"><?= $this->localization->t('general.search') ?></label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="<?= $this->localization->t('vendor.search_services_placeholder') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="w-full md:w-48">
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1"><?= $this->localization->t('general.category') ?></label>
            <select id="category" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value=""><?= $this->localization->t('general.all_categories') ?></option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-full md:w-48">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1"><?= $this->localization->t('general.status') ?></label>
            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value=""><?= $this->localization->t('general.all_statuses') ?></option>
                <option value="active" <?= (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.active') ?>
                </option>
                <option value="inactive" <?= (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : '' ?>>
                    <?= $this->localization->t('vendor.inactive') ?>
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

<!-- Services list -->
<div class="bg-white rounded-lg shadow-md">
    <?php if (empty($services)): ?>
    <div class="p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-print text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2"><?= $this->localization->t('vendor.no_services_found') ?></h3>
        <p class="text-gray-600 mb-6"><?= $this->localization->t('vendor.no_services_found_desc') ?></p>
        <a href="/vendor/services/add" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <?= $this->localization->t('vendor.add_service') ?>
        </a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.service') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.category') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.base_price') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('vendor.quote_requests') ?>
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
                <?php foreach ($services as $service): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <?php if (isset($service['media']) && !empty($service['media'])): ?>
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-md object-cover" src="<?= $service['media'][0]['path'] ?>" alt="<?= htmlspecialchars($service['title']) ?>">
                            </div>
                            <?php else: ?>
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                <i class="fas fa-print text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                            <div class="<?= $marginStart ?>-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($service['title']) ?>
                                </div>
                                <div class="text-sm text-gray-500"><?= substr(strip_tags($service['description']), 0, 50) ?>...</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($service['category_name']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= $this->localization->t('general.currency_symbol') ?><?= number_format($service['base_price'], 2) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= $service['request_count'] ?? 0 ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($service['status'] === 'active'): ?>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            <?= $this->localization->t('vendor.active') ?>
                        </span>
                        <?php else: ?>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            <?= $this->localization->t('vendor.inactive') ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-3">
                            <a href="/vendor/services/edit/<?= $service['id'] ?>" class="text-blue-600 hover:text-blue-900"><?= $this->localization->t('general.edit') ?></a>
                            <a href="/services/<?= $service["slug_{$this->localization->getCurrentLanguage()}"] ?>" target="_blank" class="text-green-600 hover:text-green-900"><?= $this->localization->t('general.view') ?></a>
                            <button type="button" data-service-id="<?= $service['id'] ?>" data-service-name="<?= htmlspecialchars($service['title']) ?>" class="delete-service text-red-600 hover:text-red-900"><?= $this->localization->t('general.delete') ?></button>
                        </div>
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
                <a href="?page=<?= $pagination['current'] - 1 ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '' ?><?= isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                    <?= $this->localization->t('general.previous') ?>
                </a>
                <?php endif; ?>

                <?php if ($pagination['current'] < $pagination['total']): ?>
                <a href="?page=<?= $pagination['current'] + 1 ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '' ?><?= isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?>" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                    <?= $this->localization->t('general.next') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Delete confirmation modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:<?= $marginStart ?>-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                            <?= $this->localization->t('vendor.delete_service_confirmation') ?>
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="delete-service-name"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form id="deleteServiceForm" method="POST" action="">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <?= $this->localization->t('general.delete') ?>
                    </button>
                </form>
                <button type="button" id="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:<?= $marginStart ?>-3 sm:w-auto sm:text-sm">
                    <?= $this->localization->t('general.cancel') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-service');
    const deleteModal = document.getElementById('deleteModal');
    const deleteServiceName = document.getElementById('delete-service-name');
    const deleteServiceForm = document.getElementById('deleteServiceForm');
    const cancelDelete = document.getElementById('cancelDelete');

    // Show delete confirmation modal
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const serviceId = this.getAttribute('data-service-id');
            const serviceName = this.getAttribute('data-service-name');

            deleteServiceName.textContent = '<?= $this->localization->t('vendor.delete_service_warning') ?> "' + serviceName + '"?';
            deleteServiceForm.action = '/vendor/services/delete/' + serviceId;
            deleteModal.classList.remove('hidden');
        });
    });

    // Hide delete confirmation modal
    cancelDelete.addEventListener('click', function() {
        deleteModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(event) {
        if (event.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });
});
</script>
