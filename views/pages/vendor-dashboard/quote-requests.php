<?php
/**
 * Vendor Quote Requests
 * File path: views/pages/vendor-dashboard/quote-requests.php
 * 
 * Shows list of quote requests for a vendor
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';

$title = $this->localization->t('vendor.quote_requests');
?>

<?php include 'views/layouts/vendor.php'; ?>

<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="flex items-center py-4 text-sm text-gray-600 <?= $textAlign ?>">
        <a href="/vendor/dashboard" class="hover:text-primary-600"><?= $this->localization->t('vendor.dashboard') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-500"><?= $this->localization->t('vendor.quote_requests') ?></span>
    </div>
    
    <!-- Header and Filters -->
    <div class="flex flex-wrap items-center justify-between mb-6">
        <h1 class="text-2xl font-bold"><?= $this->localization->t('vendor.quote_requests') ?></h1>
        
        <!-- Status Filter -->
        <div class="flex mt-3 md:mt-0">
            <a href="/vendor/quote-requests" class="px-3 py-2 <?= !isset($_GET['status']) ? 'bg-primary-100 text-primary-700 font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> rounded-l-md">
                <?= $this->localization->t('vendor.all_requests') ?>
            </a>
            <a href="/vendor/quote-requests?status=pending" class="px-3 py-2 <?= isset($_GET['status']) && $_GET['status'] === 'pending' ? 'bg-primary-100 text-primary-700 font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <?= $this->localization->t('vendor.pending') ?>
            </a>
            <a href="/vendor/quote-requests?status=quoted" class="px-3 py-2 <?= isset($_GET['status']) && $_GET['status'] === 'quoted' ? 'bg-primary-100 text-primary-700 font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <?= $this->localization->t('vendor.quoted') ?>
            </a>
            <a href="/vendor/quote-requests?status=accepted" class="px-3 py-2 <?= isset($_GET['status']) && $_GET['status'] === 'accepted' ? 'bg-primary-100 text-primary-700 font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> rounded-r-md">
                <?= $this->localization->t('vendor.accepted') ?>
            </a>
        </div>
    </div>
    
    <?php if ($isFreemiumLimitReached && !$hasActiveSubscription): ?>
    <!-- Subscription Warning -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="<?= $marginStart ?>-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    <?= $this->localization->t('vendor.subscription_limit_warning') ?>
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p><?= $this->localization->t('vendor.used_requests', ['used' => $usedQuoteCount, 'total' => $freeLimit]) ?></p>
                </div>
                <div class="mt-3">
                    <a href="/vendor/subscription" class="text-sm font-medium text-yellow-800 hover:text-yellow-700 inline-flex items-center">
                        <?= $this->localization->t('vendor.upgrade_now') ?>
                        <svg class="h-5 w-5 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Quote Requests List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (!empty($quoteRequests)): ?>
            <!-- Desktop Table (hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('vendor.request_id') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('vendor.service') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('vendor.customer') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('vendor.date') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-left">
                                <?= $this->localization->t('vendor.status') ?>
                            </th>
                            <th class="p-4 border-b border-gray-200 text-center">
                                <?= $this->localization->t('vendor.action') ?>
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
                                    <?= htmlspecialchars($request['contact_name']) ?>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                </td>
                                <td class="p-4 border-b border-gray-200">
                                    <div class="flex items-center">
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
                                        
                                        <?php if ($request['has_quoted']): ?>
                                            <span class="inline-flex <?= $marginStart ?>-2 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                <?= $this->localization->t('vendor.quoted') ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-4 border-b border-gray-200 text-center">
                                    <a 
                                        href="/vendor/quote-requests/<?= $request['id'] ?>" 
                                        class="inline-flex items-center px-3 py-1 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm"
                                    >
                                        <?php if (!$request['has_quoted'] && $request['status'] !== 'accepted'): ?>
                                            <?= $this->localization->t('vendor.respond') ?>
                                        <?php else: ?>
                                            <?= $this->localization->t('vendor.view') ?>
                                        <?php endif; ?>
                                    </a>
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
                                    <div class="text-sm text-gray-600"><?= htmlspecialchars($request['contact_name']) ?></div>
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
                                
                                <?php if ($request['has_quoted']): ?>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        <?= $this->localization->t('vendor.quoted') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <a 
                                href="/vendor/quote-requests/<?= $request['id'] ?>" 
                                class="inline-flex items-center px-3 py-1 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm"
                            >
                                <?php if (!$request['has_quoted'] && $request['status'] !== 'accepted'): ?>
                                    <?= $this->localization->t('vendor.respond') ?>
                                <?php else: ?>
                                    <?= $this->localization->t('vendor.view') ?>
                                <?php endif; ?>
                            </a>
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
                                <a href="/vendor/quote-requests?page=<?= $page - 1 ?><?= isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : '' ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
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
                                    <a href="/vendor/quote-requests?page=<?= $i ?><?= isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : '' ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
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
                                <a href="/vendor/quote-requests?page=<?= $page + 1 ?><?= isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : '' ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
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
                    <?= $this->localization->t('vendor.no_quote_requests') ?>
                </h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    <?= $this->localization->t('vendor.no_quote_requests_desc') ?>
                </p>
                <a href="/vendor/services" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <?= $this->localization->t('vendor.manage_services') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
