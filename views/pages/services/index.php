<?php
/**
 * Services Index Page (continued)
 *
 * File path: views/pages/services/index.php
 */

// Second part of the services index.php file
?>

                <!-- Services display -->
                <?php if (!empty($viewData['services'])): ?>
                    <div class="flex flex-wrap -mx-3">
                        <?php foreach ($viewData['services'] as $service): ?>
                            <?php
                                // Include the service card component with medium size
                                $size = 'medium';
                                include 'views/components/service-card.php';
                            ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($viewData['totalPages'] > 1): ?>
                        <div class="mt-8 flex justify-center">
                            <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php
                                // Previous page link
                                $prevPageUrl = ($viewData['currentPage'] > 1)
                                    ? $this->addPageToCurrentUrl($viewData['currentPage'] - 1)
                                    : '#';
                                $prevDisabled = ($viewData['currentPage'] <= 1) ? 'opacity-50 cursor-not-allowed' : '';

                                // Next page link
                                $nextPageUrl = ($viewData['currentPage'] < $viewData['totalPages'])
                                    ? $this->addPageToCurrentUrl($viewData['currentPage'] + 1)
                                    : '#';
                                $nextDisabled = ($viewData['currentPage'] >= $viewData['totalPages']) ? 'opacity-50 cursor-not-allowed' : '';
                                ?>

                                <!-- Previous page -->
                                <a
                                    href="<?= $prevPageUrl ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-<?= $isRtl ? 'right' : 'left' ?> border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= $prevDisabled ?>"
                                    <?= ($viewData['currentPage'] <= 1) ? 'aria-disabled="true"' : '' ?>
                                >
                                    <span class="sr-only"><?= $this->localization->t('general.previous') ?></span>
                                    <i class="fas <?= $isRtl ? 'fa-chevron-right' : 'fa-chevron-left' ?> h-5 w-5"></i>
                                </a>

                                <!-- Page numbers -->
                                <?php
                                $range = 2; // How many pages to show on each side of current page
                                $startPage = max(1, $viewData['currentPage'] - $range);
                                $endPage = min($viewData['totalPages'], $viewData['currentPage'] + $range);

                                // Always show first page
                                if ($startPage > 1) {
                                    echo '<a href="' . $this->addPageToCurrentUrl(1) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
                                    if ($startPage > 2) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                                    }
                                }

                                // Page links
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $isCurrentPage = $i === $viewData['currentPage'];
                                    $pageClass = $isCurrentPage
                                        ? 'relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600 z-10'
                                        : 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50';

                                    echo '<a href="' . $this->addPageToCurrentUrl($i) . '" class="' . $pageClass . '" ' . ($isCurrentPage ? 'aria-current="page"' : '') . '>' . $i . '</a>';
                                }

                                // Always show last page
                                if ($endPage < $viewData['totalPages']) {
                                    if ($endPage < $viewData['totalPages'] - 1) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                                    }
                                    echo '<a href="' . $this->addPageToCurrentUrl($viewData['totalPages']) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $viewData['totalPages'] . '</a>';
                                }
                                ?>

                                <!-- Next page -->
                                <a
                                    href="<?= $nextPageUrl ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-<?= $isRtl ? 'left' : 'right' ?> border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= $nextDisabled ?>"
                                    <?= ($viewData['currentPage'] >= $viewData['totalPages']) ? 'aria-disabled="true"' : '' ?>
                                >
                                    <span class="sr-only"><?= $this->localization->t('general.next') ?></span>
                                    <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> h-5 w-5"></i>
                                </a>
                            </nav>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- No results -->
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-search fa-3x"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                            <?= $this->localization->t('services.no_services_found') ?>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            <?= $this->localization->t('services.try_different_filters') ?>
                        </p>
                        <a href="/services" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                            <?= $this->localization->t('services.clear_all_filters') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper function to update URL with sort parameter
    function addSortToCurrentUrl(sortValue) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortValue);

        // Reset page parameter when sorting changes
        if (url.searchParams.has('page')) {
            url.searchParams.set('page', '1');
        }

        return url.href;
    }

    // Helper function to update URL with page parameter
    function addPageToCurrentUrl(pageNumber) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNumber);
        return url.href;
    }
</script>
