<?php
/**
 * Quote Request History
 * File path: views/pages/quotes/history.php
 *
 * Shows the history of quote requests for the logged-in user
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';

$title = $this->localization->t('quotes.history_title');
?>

<?php include 'views/layouts/main.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center py-4 text-sm text-gray-600 <?= $textAlign ?>">
        <a href="/" class="hover:text-primary-600"><?= $this->localization->t('nav.home') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-500"><?= $this->localization->t('nav.quote_requests') ?></span>
    </div>

    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl font-bold"><?= $this->localization->t('quotes.history_title') ?></h1>

        <a href="/services" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <svg class="h-5 w-5 <?= $marginEnd ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <?= $this->localization->t('quotes.new_request') ?>
        </a>
    </div>

    <!-- Quote Requests List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (!empty($quoteRequests)): ?>
            <!-- Desktop Table (hidden on mobile) -->
            <div class="hidden md:block">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('quotes.request_id') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('quotes.service') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('quotes.vendor') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('quotes.date') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('quotes.status') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('quotes.quotes') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-center">
                                <?= $this->localization->t('general.actions') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quoteRequests as $request): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-b border-gray-200">
                                    #<?= $request['id'] ?>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <?= $request["title_{$language}"] ?>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <?= $request["company_name_{$language}"] ?>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                        <?php
                                        switch($request['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'quoted':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'accepted':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>
                                    ">
                                        <?= $this->localization->t('quotes.status_' . $request['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <?php if ($request['quote_count'] > 0): ?>
                                        <span class="text-primary-600 font-medium">
                                            <?= $request['quote_count'] ?> <?= $this->localization->t('quotes.received') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-500">
                                            <?= $this->localization->t('quotes.pending') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 border-b border-gray-200 text-center">
                                    <a href="/quotes/track/<?= $request['id'] ?>" class="inline-flex items-center text-primary-600 hover:text-primary-700">
                                        <?= $this->localization->t('quotes.view') ?>
                                    </a>

                                    <?php if ($request['quote_count'] > 1): ?>
                                        <span class="px-2 text-gray-300">|</span>
                                        <a href="/quotes/compare/<?= $request['id'] ?>" class="inline-flex items-center text-primary-600 hover:text-primary-700">
                                            <?= $this->localization->t('quotes.compare') ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile List (visible only on mobile) -->
            <div class="md:hidden">
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($quoteRequests as $request): ?>
                        <li class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-medium"><?= $request["title_{$language}"] ?></div>
                                    <div class="text-sm text-gray-600"><?= $request["company_name_{$language}"] ?></div>
                                </div>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                    <?php
                                    switch($request['status']) {
                                        case 'pending':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'quoted':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'accepted':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'cancelled':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                ">
                                    <?= $this->localization->t('quotes.status_' . $request['status']) ?>
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-sm mb-3">
                                <div class="text-gray-500">
                                    #<?= $request['id'] ?> • <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                </div>

                                <div>
                                    <?php if ($request['quote_count'] > 0): ?>
                                        <span class="text-primary-600 font-medium">
                                            <?= $request['quote_count'] ?> <?= $this->localization->t('quotes.received') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-500">
                                            <?= $this->localization->t('quotes.pending') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex">
                                <a href="/quotes/track/<?= $request['id'] ?>" class="inline-flex items-center px-3 py-1 bg-primary-50 text-primary-700 rounded-md text-sm">
                                    <?= $this->localization->t('quotes.view') ?>
                                </a>

                                <?php if ($request['quote_count'] > 1): ?>
                                    <a href="/quotes/compare/<?= $request['id'] ?>" class="<?= $marginStart ?>-2 inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-md text-sm">
                                        <?= $this->localization->t('quotes.compare') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="px-4 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <?= $this->localization->t('general.showing_page', ['page' => $page, 'total' => $totalPages]) ?>
                        </div>

                        <div class="flex space-x-1">
                            <?php if ($page > 1): ?>
                                <a href="/quotes/history?page=<?= $page - 1 ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    <span class="sr-only"><?= $this->localization->t('general.previous') ?></span>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $page): ?>
                                    <span class="inline-flex items-center px-4 py-2 border border-primary-500 rounded-md bg-primary-50 text-primary-700">
                                        <?= $i ?>
                                    </span>
                                <?php elseif (($i <= 3 || $i >= $totalPages - 2) || abs($i - $page) <= 1): ?>
                                    <a href="/quotes/history?page=<?= $i ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
                                        <?= $i ?>
                                    </a>
                                <?php elseif ($i === 4 && $page > 5): ?>
                                    <span class="inline-flex items-center px-4 py-2 text-gray-700">
                                        ...
                                    </span>
                                <?php elseif ($i === $totalPages - 3 && $page < $totalPages - 4): ?>
                                    <span class="inline-flex items-center px-4 py-2 text-gray-700">
                                        ...
                                    </span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="/quotes/history?page=<?= $page + 1 ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <span class="sr-only"><?= $this->localization->t('general.next') ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="text-xl font-medium text-gray-900 mb-2">
                    <?= $this->localization->t('quotes.no_requests_yet') ?>
                </h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    <?= $this->localization->t('quotes.no_requests_desc') ?>
                </p>
                <a href="/services" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <svg class="h-5 w-5 <?= $marginEnd ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <?= $this->localization->t('quotes.browse_services') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
