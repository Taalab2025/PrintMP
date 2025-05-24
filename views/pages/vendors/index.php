<?php
/**
 * Vendors Index Page
 *
 * File path: views/pages/vendors/index.php
 *
 * Displays all vendors with filtering and search options
 */

// Check if it's an RTL language
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$floatDir = $isRtl ? 'float-right' : 'float-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
$paddingStart = $isRtl ? 'pr' : 'pl';
$paddingEnd = $isRtl ? 'pl' : 'pr';
?>

<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-600 mb-6 <?= $textAlign ?>">
            <a href="/" class="hover:underline"><?= $this->localization->t('general.home') ?></a>
            <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>

            <?php if (isset($viewData['currentCategory'])): ?>
                <a href="/vendors" class="hover:underline"><?= $this->localization->t('vendors.all_vendors') ?></a>
                <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>
                <span class="text-gray-900"><?= htmlspecialchars($viewData['currentCategory']['name']) ?></span>
            <?php elseif (isset($viewData['filters']['search_term'])): ?>
                <a href="/vendors" class="hover:underline"><?= $this->localization->t('vendors.all_vendors') ?></a>
                <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>
                <span class="text-gray-900"><?= $this->localization->t('vendors.search_results') ?></span>
            <?php else: ?>
                <span class="text-gray-900"><?= $this->localization->t('vendors.all_vendors') ?></span>
            <?php endif; ?>
        </div>

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-3 <?= $textAlign ?>"><?= htmlspecialchars($viewData['title']) ?></h1>

            <?php if (isset($viewData['currentCategory']) && !empty($viewData['currentCategory']['description'])): ?>
                <div class="text-gray-600 <?= $textAlign ?>">
                    <?= htmlspecialchars($viewData['currentCategory']['description']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Applied filters -->
        <?php if (!empty($viewData['filterDescriptions'])): ?>
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <div class="flex flex-wrap items-center">
                    <span class="font-medium mr-3 mb-2"><?= $this->localization->t('vendors.applied_filters') ?>:</span>

                    <?php foreach ($viewData['filterDescriptions'] as $filter): ?>
                        <div class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm ${marginEnd}-2 mb-2 flex items-center">
                            <span class="font-medium"><?= htmlspecialchars($filter['label']) ?>:</span>
                            <span class="mx-1"><?= htmlspecialchars($filter['value']) ?></span>
                            <a href="<?= htmlspecialchars($filter['remove_url']) ?>" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle ml-1"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($viewData['filterDescriptions']) > 1): ?>
                        <a href="/vendors" class="text-blue-600 hover:underline text-sm mb-2">
                            <?= $this->localization->t('vendors.clear_all_filters') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex flex-wrap -mx-4">
            <!-- Sidebar filters -->
            <div class="w-full lg:w-1/4 px-4 mb-6 lg:mb-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <!-- Search bar -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3 <?= $textAlign ?>"><?= $this->localization->t('vendors.search') ?></h3>
                        <form action="/vendors/search" method="GET" class="flex">
                            <input
                                type="text"
                                name="q"
                                placeholder="<?= $this->localization->t('vendors.search_placeholder') ?>"
                                class="flex-grow px-4 py-2 border rounded-<?= $isRtl ? 'right' : 'left' ?> focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?= isset($viewData['filters']['search_term']) ? htmlspecialchars($viewData['filters']['search_term']) : '' ?>"
                            >
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-<?= $isRtl ? 'left' : 'right' ?> hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Categories filter -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3 <?= $textAlign ?>"><?= $this->localization->t('vendors.filter_by_category') ?></h3>
                        <ul class="<?= $textAlign ?>">
                            <?php foreach ($viewData['categories'] as $category): ?>
                                <li class="mb-2">
                                    <a
                                        href="/vendors?category=<?= htmlspecialchars($category['slug']) ?>"
                                        class="flex items-center text-gray-700 hover:text-blue-600 <?= isset($viewData['filters']['category_id']) && $viewData['filters']['category_id'] == $category['id'] ? 'font-semibold text-blue-600' : '' ?>"
                                    >
                                        <?php if (!empty($category['icon'])): ?>
                                            <i class="<?= htmlspecialchars($category['icon']) ?> <?= $marginEnd ?>-2 w-5 text-center"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Location filter -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3 <?= $textAlign ?>"><?= $this->localization->t('vendors.filter_by_location') ?></h3>
                        <form action="/vendors" method="GET" class="space-y-3">
                            <!-- Preserve other filters -->
                            <?php foreach ($viewData['filters'] as $key => $value): ?>
                                <?php if ($key !== 'location' && $key !== 'page'): ?>
                                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <input
                                type="text"
                                name="location"
                                placeholder="<?= $this->localization->t('vendors.enter_location') ?>"
                                class="w-full px-3 py-2 border rounded text-sm <?= $textAlign ?>"
                                value="<?= isset($viewData['filters']['location']) ? htmlspecialchars($viewData['filters']['location']) : '' ?>"
                            >

                            <button
                                type="submit"
                                class="w-full bg-gray-200 text-gray-800 py-2 rounded text-sm hover:bg-gray-300 transition-colors"
                            >
                                <?= $this->localization->t('vendors.apply_filter') ?>
                            </button>
                        </form>
                    </div>

                    <!-- Rating filter -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3 <?= $textAlign ?>"><?= $this->localization->t('vendors.filter_by_rating') ?></h3>
                        <form action="/vendors" method="GET">
                            <!-- Preserve other filters -->
                            <?php foreach ($viewData['filters'] as $key => $value): ?>
                                <?php if ($key !== 'min_rating' && $key !== 'page'): ?>
                                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php
                            $currentRating = isset($viewData['filters']['min_rating']) ? (int)$viewData['filters']['min_rating'] : 0;

                            for ($stars = 5; $stars >= 1; $stars--):
                                $isSelected = $currentRating == $stars;
                            ?>
                                <div class="mb-2">
                                    <a
                                        href="/vendors?<?= http_build_query(array_merge($viewData['filters'], ['min_rating' => $stars, 'page' => 1])) ?>"
                                        class="flex items-center rounded hover:bg-gray-100 p-2 <?= $isSelected ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' ?>"
                                    >
                                        <div class="flex text-yellow-400">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas <?= $i <= $stars ? 'fa-star' : 'fa-star text-gray-300' ?> <?= $marginEnd ?>-1"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="<?= $marginStart ?>-1">
                                            <?= $this->localization->t('vendors.and_up') ?>
                                        </span>
                                    </a>
                                </div>
                            <?php endfor; ?>

                            <?php if ($currentRating > 0): ?>
                                <div class="mt-3">
                                    <a
                                        href="<?= $this->removeFilterFromCurrentUrl('min_rating') ?>"
                                        class="text-blue-600 hover:underline text-sm"
                                    >
                                        <?= $this->localization->t('vendors.clear_rating_filter') ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Vendors grid -->
            <div class="w-full lg:w-3/4 px-4">
                <!-- Results info and sorting -->
                <div class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap items-center justify-between mb-6">
                    <div class="text-gray-600 mb-2 sm:mb-0">
                        <?= $this->localization->t('vendors.showing_results', [
                            'from' => min(($viewData['currentPage'] - 1) * 12 + 1, $viewData['totalVendors']),
                            'to' => min($viewData['currentPage'] * 12, $viewData['totalVendors']),
                            'total' => $viewData['totalVendors']
                        ]) ?>
                    </div>

                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2"><?= $this->localization->t('vendors.sort_by') ?>:</span>
                        <select
                            id="sort-selector"
                            class="border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="window.location.href = this.value"
                        >
                            <option value="<?= $this->addSortToCurrentUrl('newest') ?>" <?= !isset($_GET['sort']) || $_GET['sort'] === 'newest' ? 'selected' : '' ?>>
                                <?= $this->localization->t('vendors.newest') ?>
                            </option>
                            <option value="<?= $this->addSortToCurrentUrl('rating_high') ?>" <?= isset($_GET['sort']) && $_GET['sort'] === 'rating_high' ? 'selected' : '' ?>>
                                <?= $this->localization->t('vendors.highest_rated') ?>
                            </option>
                            <option value="<?= $this->addSortToCurrentUrl('service_count') ?>" <?= isset($_GET['sort']) && $_GET['sort'] === 'service_count' ? 'selected' : '' ?>>
                                <?= $this->localization->t('vendors.most_services') ?>
                            </option>
                            <option value="<?= $this->addSortToCurrentUrl('name') ?>" <?= isset($_GET['sort']) && $_GET['sort'] === 'name' ? 'selected' : '' ?>>
                                <?= $this->localization->t('vendors.name') ?>
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Vendors display -->
                <?php if (!empty($viewData['vendors'])): ?>
                    <div class="flex flex-wrap -mx-3">
                        <?php foreach ($viewData['vendors'] as $vendor): ?>
                            <div class="w-full md:w-1/2 lg:w-1/3 p-3">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1 h-full flex flex-col">
                                    <!-- Vendor header with logo -->
                                    <div class="p-6 flex items-center">
                                        <?php if (!empty($vendor['logo'])): ?>
                                            <img
                                                src="<?= htmlspecialchars($vendor['logo']) ?>"
                                                alt="<?= htmlspecialchars($vendor['company_name']) ?>"
                                                class="w-16 h-16 rounded-full object-cover <?= $marginEnd ?>-3"
                                            >
                                        <?php else: ?>
                                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center <?= $marginEnd ?>-3">
                                                <i class="fas fa-store text-gray-500 text-xl"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <h3 class="font-semibold text-lg <?= $textAlign ?>">
                                                <a href="/vendors/<?= htmlspecialchars($vendor['id']) ?>" class="text-gray-900 hover:text-blue-600 transition-colors">
                                                    <?= htmlspecialchars($vendor['company_name']) ?>
                                                </a>
                                            </h3>

                                            <?php if (isset($vendor['rating'])): ?>
                                                <div class="flex items-center">
                                                    <div class="flex text-yellow-400">
                                                        <?php
                                                        $rating = round($vendor['rating'] * 2) / 2; // Round to nearest 0.5
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            if ($rating >= $i) {
                                                                echo '<i class="fas fa-star"></i>';
                                                            } else if ($rating >= $i - 0.5) {
                                                                echo '<i class="fas fa-star-half-alt"></i>';
                                                            } else {
                                                                echo '<i class="far fa-star"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <span class="text-gray-600 text-sm <?= $marginStart ?>-1">
                                                        (<?= isset($vendor['review_count']) ? $vendor['review_count'] : 0 ?>)
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Vendor details -->
                                    <div class="px-6 pb-4 flex-grow">
                                        <?php if (!empty($vendor['location'])): ?>
                                            <div class="flex items-center text-gray-600 text-sm mb-2 <?= $textAlign ?>">
                                                <i class="fas fa-map-marker-alt w-5 text-gray-400 <?= $marginEnd ?>-1"></i>
                                                <?= htmlspecialchars($vendor['location']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($vendor['service_count']) && $vendor['service_count'] > 0): ?>
                                            <div class="flex items-center text-gray-600 text-sm mb-3 <?= $textAlign ?>">
                                                <i class="fas fa-print w-5 text-gray-400 <?= $marginEnd ?>-1"></i>
                                                <?= $vendor['service_count'] ?> <?= $this->localization->t('vendors.services') ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($vendor['description'])): ?>
                                            <div class="text-gray-600 text-sm mb-4 <?= $textAlign ?>">
                                                <?= substr(htmlspecialchars($vendor['description']), 0, 100) ?>
                                                <?php if (strlen($vendor['description']) > 100): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Action links -->
                                    <div class="px-6 pb-6 mt-auto">
                                        <div class="flex flex-wrap">
                                            <a
                                                href="/vendors/<?= htmlspecialchars($vendor['id']) ?>"
                                                class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors flex-grow text-center"
                                            >
                                                <?= $this->localization->t('vendors.view_profile') ?>
                                            </a>

                                            <a
                                                href="/services?vendor=<?= htmlspecialchars($vendor['id']) ?>"
                                                class="bg-gray-200 text-gray-800 px-3 py-2 rounded text-sm hover:bg-gray-300 transition-colors flex-grow text-center <?= $marginStart ?>-2"
                                            >
                                                <?= $this->localization->t('vendors.view_services') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <?= $this->localization->t('vendors.no_vendors_found') ?>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            <?= $this->localization->t('vendors.try_different_filters') ?>
                        </p>
                        <a href="/vendors" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                            <?= $this->localization->t('vendors.clear_all_filters') ?>
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

    // Helper function to remove a filter from the current URL
    function removeFilterFromCurrentUrl(paramToRemove) {
        const url = new URL(window.location.href);
        url.searchParams.delete(paramToRemove);

        // Keep page parameter only if it's not 1
        if (url.searchParams.get('page') === '1') {
            url.searchParams.delete('page');
        }

        return url.href;
    }
</script>
