<?php
/**
 * Admin Reviews Management View
 * File path: views/pages/admin/reviews.php
 * Admin interface for managing customer reviews
 */

$language = $this->localization->getCurrentLanguage();
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';

// Get data passed from controller
$reviews = $reviews ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$filters = $filters ?? [];
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="<?= $textAlign ?>">
            <h1 class="text-2xl font-bold text-gray-900">
                <?= $this->localization->t('admin.review_management') ?>
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                <?= $this->localization->t('admin.review_management_desc') ?>
            </p>
        </div>

        <div class="mt-4 sm:mt-0">
            <div class="flex items-center space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                <span class="text-sm text-gray-600">
                    <?= $this->localization->t('admin.total_reviews') ?>: <?= number_format($totalReviews ?? 0) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="/admin/reviews" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1 <?= $textAlign ?>">
                        <?= $this->localization->t('admin.search') ?>
                    </label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                           placeholder="<?= $this->localization->t('admin.search_reviews_placeholder') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 <?= $textAlign ?>">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1 <?= $textAlign ?>">
                        <?= $this->localization->t('admin.status') ?>
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 <?= $textAlign ?>">
                        <option value=""><?= $this->localization->t('admin.all_statuses') ?></option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>
                            <?= $this->localization->t('admin.active') ?>
                        </option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>
                            <?= $this->localization->t('admin.pending') ?>
                        </option>
                        <option value="hidden" <?= ($filters['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>
                            <?= $this->localization->t('admin.hidden') ?>
                        </option>
                    </select>
                </div>

                <!-- Rating Filter -->
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-1 <?= $textAlign ?>">
                        <?= $this->localization->t('admin.rating') ?>
                    </label>
                    <select name="rating"
                            id="rating"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 <?= $textAlign ?>">
                        <option value=""><?= $this->localization->t('admin.all_ratings') ?></option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= ($filters['rating'] ?? '') == $i ? 'selected' : '' ?>>
                                <?= $i ?> <?= $i === 1 ? $this->localization->t('admin.star') : $this->localization->t('admin.stars') ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search <?= $marginEnd ?>-2"></i>
                        <?= $this->localization->t('admin.filter') ?>
                    </button>

                    <a href="/admin/reviews"
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-12">
                <i class="fas fa-star text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    <?= $this->localization->t('admin.no_reviews_found') ?>
                </h3>
                <p class="text-gray-600">
                    <?= $this->localization->t('admin.no_reviews_desc') ?>
                </p>
            </div>
        <?php else: ?>
            <!-- Desktop View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.customer') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.vendor') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.service') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.rating') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.status') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.date') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('admin.actions') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($reviews as $review): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap <?= $textAlign ?>">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($review['user_name']) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap <?= $textAlign ?>">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($review["company_name_{$language}"]) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap <?= $textAlign ?>">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($review["service_title_{$language}"]) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap <?= $textAlign ?>">
                                    <div class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?>">
                                        <div class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?>">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-sm <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?> <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="<?= $marginStart ?>-2 text-sm text-gray-600">
                                            (<?= $review['rating'] ?>)
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap <?= $textAlign ?>">
                                    <?php
                                    $statusColors = [
                                        'active' => 'green',
                                        'pending' => 'yellow',
                                        'hidden' => 'red'
                                    ];
                                    $statusColor = $statusColors[$review['status']] ?? 'gray';
                                    ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800">
                                        <?= $this->localization->t('admin.status_' . $review['status']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 <?= $textAlign ?>">
                                    <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?= $textAlign ?>">
                                    <div class="flex items-center space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                                        <button onclick="viewReview(<?= $review['id'] ?>)"
                                                class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <?php if ($review['status'] === 'pending'): ?>
                                            <form method="POST" action="/admin/reviews/<?= $review['id'] ?>/approve" class="inline">
                                                <?= $this->session->getCSRFTokenField() ?>
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-900 transition-colors"
                                                        title="<?= $this->localization->t('admin.approve') ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($review['status'] === 'active'): ?>
                                            <form method="POST" action="/admin/reviews/<?= $review['id'] ?>/hide" class="inline">
                                                <?= $this->session->getCSRFTokenField() ?>
                                                <button type="submit"
                                                        class="text-orange-600 hover:text-orange-900 transition-colors"
                                                        title="<?= $this->localization->t('admin.hide') ?>"
                                                        onclick="return confirm('<?= $this->localization->t('admin.confirm_hide_review') ?>')">
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="POST" action="/admin/reviews/<?= $review['id'] ?>/delete" class="inline">
                                            <?= $this->session->getCSRFTokenField() ?>
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="<?= $this->localization->t('admin.delete') ?>"
                                                    onclick="return confirm('<?= $this->localization->t('admin.confirm_delete_review') ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="md:hidden">
                <?php foreach ($reviews as $review): ?>
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="<?= $textAlign ?>">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($review['user_name']) ?>
                                </h3>
                                <p class="text-xs text-gray-600">
                                    <?= htmlspecialchars($review["company_name_{$language}"]) ?>
                                </p>
                            </div>

                            <?php
                            $statusColors = [
                                'active' => 'green',
                                'pending' => 'yellow',
                                'hidden' => 'red'
                            ];
                            $statusColor = $statusColors[$review['status']] ?? 'gray';
                            ?>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800">
                                <?= $this->localization->t('admin.status_' . $review['status']) ?>
                            </span>
                        </div>

                        <div class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?> mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star text-sm <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?> <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                            <?php endfor; ?>
                            <span class="<?= $marginStart ?>-2 text-sm text-gray-600">
                                (<?= $review['rating'] ?>)
                            </span>
                        </div>

                        <p class="text-sm text-gray-900 mb-3 <?= $textAlign ?>">
                            <?= htmlspecialchars($review["service_title_{$language}"]) ?>
                        </p>

                        <?php if ($review['comment']): ?>
                            <p class="text-sm text-gray-600 mb-3 <?= $textAlign ?>">
                                "<?= htmlspecialchars(substr($review['comment'], 0, 100)) ?><?= strlen($review['comment']) > 100 ? '...' : '' ?>"
                            </p>
                        <?php endif; ?>

                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">
                                <?= date('M d, Y', strtotime($review['created_at'])) ?>
                            </span>

                            <div class="flex items-center space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                                <button onclick="viewReview(<?= $review['id'] ?>)"
                                        class="text-blue-600 hover:text-blue-900 transition-colors text-sm">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <?php if ($review['status'] === 'pending'): ?>
                                    <form method="POST" action="/admin/reviews/<?= $review['id'] ?>/approve" class="inline">
                                        <?= $this->session->getCSRFTokenField() ?>
                                        <button type="submit"
                                                class="text-green-600 hover:text-green-900 transition-colors text-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($review['status'] === 'active'): ?>
                                    <form method="POST" action="/admin/reviews/<?= $review['id'] ?>/hide" class="inline">
                                        <?= $this->session->getCSRFTokenField() ?>
                                        <button type="submit"
                                                class="text-orange-600 hover:text-orange-900 transition-colors text-sm"
                                                onclick="return confirm('<?= $this->localization->t('admin.confirm_hide_review') ?>')">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="POST" action="/admin/reviews/<?= $review['id'] ?>/delete" class="inline">
                                    <?= $this->session->getCSRFTokenField() ?>
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition-colors text-sm"
                                            onclick="return confirm('<?= $this->localization->t('admin.confirm_delete_review') ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 <?= $textAlign ?>">
                <?= $this->localization->t('admin.showing_results', [
                    'start' => (($currentPage - 1) * 20) + 1,
                    'end' => min($currentPage * 20, $totalReviews ?? 0),
                    'total' => $totalReviews ?? 0
                ]) ?>
            </div>

            <nav class="flex items-center space-x-2 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>"
                       class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <?= $this->localization->t('admin.previous') ?>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a href="?page=<?= $i ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>"
                       class="px-3 py-2 text-sm font-medium <?= $i === $currentPage ? 'text-blue-600 bg-blue-50 border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50' ?> border rounded-md">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>"
                       class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <?= $this->localization->t('admin.next') ?>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Review Detail Modal -->
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <?= $this->localization->t('admin.review_details') ?>
            </h3>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="reviewModalContent">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
function viewReview(reviewId) {
    // Show loading
    document.getElementById('reviewModalContent').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> <?= $this->localization->t('admin.loading') ?></div>';
    document.getElementById('reviewModal').classList.remove('hidden');

    // Fetch review details via AJAX
    fetch(`/admin/reviews/${reviewId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('reviewModalContent').innerHTML = data.html;
            } else {
                document.getElementById('reviewModalContent').innerHTML = '<div class="text-center py-4 text-red-600"><?= $this->localization->t('admin.error_loading_review') ?></div>';
            }
        })
        .catch(error => {
            document.getElementById('reviewModalContent').innerHTML = '<div class="text-center py-4 text-red-600"><?= $this->localization->t('admin.error_loading_review') ?></div>';
        });
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewModal();
    }
});
</script>
