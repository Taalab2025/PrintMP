<?php
/**
 * Admin Categories Page
 * file path: views/pages/admin/categories.php
 */

// Include the admin layout
require_once 'views/layouts/admin.php';

// Start content section
ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold"><?= $this->localization->t('categories.admin_title') ?></h1>
        <a href="/admin/categories/create" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
            <i class="fas fa-plus <?= $this->localization->isRtl() ? 'ml-2' : 'mr-2' ?>"></i> <?= $this->localization->t('categories.create_new') ?>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 <?= $this->localization->isRtl() ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('categories.name_' . $language) ?>
                    </th>
                    <th scope="col" class="px-6 py-3 <?= $this->localization->isRtl() ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('categories.parent_category') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 <?= $this->localization->isRtl() ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('categories.status') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 <?= $this->localization->isRtl() ? 'text-right' : 'text-left' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('categories.featured') ?>
                    </th>
                    <th scope="col" class="px-6 py-3 <?= $this->localization->isRtl() ? 'text-left' : 'text-right' ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= $this->localization->t('general.actions') ?>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <?= $this->localization->t('general.no_records_found') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if (!empty($category['icon'])): ?>
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-primary-100 rounded-full <?= $this->localization->isRtl() ? 'ml-3' : 'mr-3' ?>">
                                            <i class="<?= $category['icon'] ?> text-primary-600"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="<?= $this->localization->isRtl() ? 'mr-4' : 'ml-4' ?>">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($category['slug']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= $category['parent_id'] ? htmlspecialchars($parentNames[$category['parent_id']] ?? '-') : $this->localization->t('categories.no_parent') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $category['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $this->localization->t('categories.' . $category['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($category['featured']): ?>
                                    <i class="fas fa-star text-yellow-400"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-gray-400"></i>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap <?= $this->localization->isRtl() ? 'text-left' : 'text-right' ?> text-sm font-medium">
                                <a href="/admin/categories/edit/<?= $category['id'] ?>" class="text-primary-600 hover:text-primary-900 <?= $this->localization->isRtl() ? 'ml-3' : 'mr-3' ?>">
                                    <?= $this->localization->t('categories.edit') ?>
                                </a>
                                <button
                                    class="text-red-600 hover:text-red-900"
                                    onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')"
                                >
                                    <?= $this->localization->t('categories.delete') ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
        <h3 class="text-lg font-bold mb-4"><?= $this->localization->t('categories.confirm_delete') ?></h3>
        <p id="delete-message" class="mb-6 text-gray-700"></p>

        <div class="flex justify-end">
            <button id="cancel-delete" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md <?= $this->localization->isRtl() ? 'ml-2' : 'mr-2' ?>">
                <?= $this->localization->t('general.cancel') ?>
            </button>
            <form id="delete-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $this->session->generateCsrfToken() ?>">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md">
                    <?= $this->localization->t('categories.delete') ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('delete-message').textContent =
            '<?= $this->localization->t('general.confirm_delete_item') ?> "' + name + '"?';
        document.getElementById('delete-form').action = '/admin/categories/delete/' + id;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    document.getElementById('cancel-delete').addEventListener('click', function() {
        document.getElementById('delete-modal').classList.add('hidden');
    });
</script>

<?php
$content = ob_get_clean();

// Render the layout with our content
echo renderAdminLayout($content);
?>
