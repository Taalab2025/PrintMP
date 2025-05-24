<?php
/**
 * Quote Request Tracking
 * File path: views/pages/quotes/track.php
 * 
 * Shows the status and details of a quote request
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
$csrfToken = $this->session->generateCsrfToken();

$title = $this->localization->t('quotes.track_request_title');
$hasQuotes = !empty($quotes);
$email = isset($_GET['email']) ? $_GET['email'] : '';
?>

<?php include 'views/layouts/main.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center py-4 text-sm text-gray-600 <?= $textAlign ?>">
        <a href="/" class="hover:text-primary-600"><?= $this->localization->t('nav.home') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="/quotes/history" class="hover:text-primary-600"><?= $this->localization->t('nav.quote_requests') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-500"><?= $this->localization->t('quotes.request') ?> #<?= $quoteRequest['id'] ?></span>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Request Header -->
        <div class="px-6 py-4 bg-gray-50 border-b <?= $textAlign ?>">
            <div class="flex flex-wrap items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold"><?= $this->localization->t('quotes.request') ?> #<?= $quoteRequest['id'] ?></h1>
                    <p class="text-gray-600"><?= date('F j, Y', strtotime($quoteRequest['created_at'])) ?></p>
                </div>
                <div class="mt-2 md:mt-0">
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                        <?php 
                        switch($quoteRequest['status']) {
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
                        <?= $this->localization->t('quotes.status_' . $quoteRequest['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Request Details -->
        <div class="p-6 <?= $textAlign ?>">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left column: Service and Request Info -->
                <div>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3">
                            <?= $this->localization->t('quotes.service_details') ?>
                        </h2>
                        <div class="border rounded-lg overflow-hidden">
                            <?php if ($service['image']): ?>
                                <img src="<?= $service['image'] ?>" alt="<?= $service["title_{$language}"] ?>" class="w-full h-48 object-cover">
                            <?php endif; ?>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2"><?= $service["title_{$language}"] ?></h3>
                                
                                <div class="flex items-center mb-3">
                                    <?php if ($vendor['logo']): ?>
                                        <img src="<?= $vendor['logo'] ?>" alt="<?= $vendor["company_name_{$language}"] ?>" class="w-6 h-6 rounded-full <?= $marginEnd ?>-2">
                                    <?php endif; ?>
                                    <span class="text-gray-600"><?= $vendor["company_name_{$language}"] ?></span>
                                </div>
                                
                                <p class="text-gray-700 text-sm mb-3"><?= mb_substr($service["description_{$language}"], 0, 150) ?>...</p>
                                
                                <a href="/services/<?= $service['id'] ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium inline-flex items-center">
                                    <?= $this->localization->t('general.view_details') ?>
                                    <svg class="h-4 w-4 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Request Options -->
                    <?php if (!empty($quoteRequest['options'])): ?>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3">
                            <?= $this->localization->t('quotes.request_options') ?>
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="space-y-2">
                                <?php foreach ($quoteRequest['options'] as $option): ?>
                                    <li class="flex flex-wrap">
                                        <span class="font-medium <?= $marginEnd ?>-2"><?= $option['option_name'] ?>:</span>
                                        <span class="text-gray-700"><?= $option['option_value'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Uploaded Files -->
                    <?php if (!empty($quoteRequest['files'])): ?>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3">
                            <?= $this->localization->t('quotes.uploaded_files') ?>
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="space-y-2">
                                <?php foreach ($quoteRequest['files'] as $file): ?>
                                    <li class="flex items-center">
                                        <?php 
                                        $fileIcon = 'document';
                                        if (strpos($file['file_type'], 'image/') === 0) {
                                            $fileIcon = 'photograph';
                                        } elseif ($file['file_type'] === 'application/pdf') {
                                            $fileIcon = 'document-text';
                                        } elseif (strpos($file['file_type'], 'application/zip') === 0 || strpos($file['file_type'], 'application/x-rar') === 0) {
                                            $fileIcon = 'archive';
                                        }
                                        ?>
                                        
                                        <svg class="h-5 w-5 text-gray-500 <?= $marginEnd ?>-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <?php if ($fileIcon === 'photograph'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            <?php elseif ($fileIcon === 'document-text'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            <?php elseif ($fileIcon === 'archive'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                            <?php else: ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            <?php endif; ?>
                                        </svg>
                                        
                                        <span class="text-gray-700"><?= $file['file_name'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Additional Message -->
                    <?php if (!empty($quoteRequest['message'])): ?>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3">
                            <?= $this->localization->t('quotes.additional_info') ?>
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($quoteRequest['message'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right column: Status and Quotes -->
                <div>
                    <!-- Status Timeline -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3">
                            <?= $this->localization->t('quotes.request_status') ?>
                        </h2>
                        <div class="relative border-l-2 border-gray-200 <?= $marginStart ?>-3 pt-2 pb-1">
                            <!-- Created -->
                            <div class="mb-6 relative">
                                <div class="absolute <?= $isRtl ? 'right' : 'left' ?>-3 <?= $isRtl ? '-translate-x-1/2' : 'translate-x-1/2' ?> -translate-y-1/2 w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="<?= $marginStart ?>-6">
                                    <h3 class="font-semibold"><?= $this->localization->t('quotes.request_submitted') ?></h3>
                                    <p class="text-sm text-gray-500"><?= date('M d, Y, H:i', strtotime($quoteRequest['created_at'])) ?></p>
                                </div>
                            </div>
                            
                            <!-- Quoted -->
                            <div class="mb-6 relative">
                                <?php $isQuoted = in_array($quoteRequest['status'], ['quoted', 'accepted']); ?>
                                <div class="absolute <?= $isRtl ? 'right' : 'left' ?>-3 <?= $isRtl ? '-translate-x-1/2' : 'translate-x-1/2' ?> -translate-y-1/2 w-6 h-6 rounded-full <?= $isQuoted ? 'bg-green-500' : 'bg-gray-300' ?> flex items-center justify-center">
                                    <?php if ($isQuoted): ?>
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="<?= $marginStart ?>-6">
                                    <h3 class="font-semibold <?= $isQuoted ? 'text-gray-900' : 'text-gray-500' ?>">
                                        <?= $this->localization->t('quotes.quote_received') ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?php if ($isQuoted): ?>
                                            <?= $this->localization->t('quotes.quotes_received_count', ['count' => count($quotes)]) ?>
                                        <?php else: ?>
                                            <?= $this->localization->t('quotes.awaiting_quotes') ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Accepted -->
                            <div class="mb-6 relative">
                                <?php $isAccepted = $quoteRequest['status'] === 'accepted'; ?>
                                <div class="absolute <?= $isRtl ? 'right' : 'left' ?>-3 <?= $isRtl ? '-translate-x-1/2' : 'translate-x-1/2' ?> -translate-y-1/2 w-6 h-6 rounded-full <?= $isAccepted ? 'bg-green-500' : 'bg-gray-300' ?> flex items-center justify-center">
                                    <?php if ($isAccepted): ?>
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="<?= $marginStart ?>-6">
                                    <h3 class="font-semibold <?= $isAccepted ? 'text-gray-900' : 'text-gray-500' ?>">
                                        <?= $this->localization->t('quotes.quote_accepted') ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?php if ($isAccepted): ?>
                                            <?= $this->localization->t('quotes.quote_accepted_desc') ?>
                                        <?php else: ?>
                                            <?= $this->localization->t('quotes.awaiting_acceptance') ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quotes Section -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-lg font-semibold">
                                <?= $this->localization->t('quotes.received_quotes') ?>
                            </h2>
                            
                            <?php if ($hasQuotes && count($quotes) > 1): ?>
                                <a href="/quotes/compare/<?= $quoteRequest['id'] ?><?= $email ? '?email=' . urlencode($email) : '' ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium inline-flex items-center">
                                    <?= $this->localization->t('quotes.compare_all') ?>
                                    <svg class="h-4 w-4 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($hasQuotes): ?>
                            <div class="space-y-4">
                                <?php foreach ($quotes as $quote): ?>
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                            <div class="flex items-center">
                                                <?php if (isset($quote['logo']) && $quote['logo']): ?>
                                                    <img src="<?= $quote['logo'] ?>" alt="<?= $quote["company_name_{$language}"] ?>" class="w-8 h-8 rounded-full <?= $marginEnd ?>-2">
                                                <?php endif; ?>
                                                <span class="font-medium"><?= $quote["company_name_{$language}"] ?></span>
                                                
                                                <?php if (isset($quote['rating'])): ?>
                                                    <div class="flex items-center <?= $marginStart ?>-2">
                                                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-600"><?= number_format($quote['rating'], 1) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                <?php 
                                                switch($quote['status']) {
                                                    case 'offered':
                                                        echo 'bg-blue-100 text-blue-800';
                                                        break;
                                                    case 'accepted':
                                                        echo 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'declined':
                                                        echo 'bg-red-100 text-red-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-100 text-gray-800';
                                                }
                                                ?>
                                            ">
                                                <?= $this->localization->t('quotes.status_' . $quote['status']) ?>
                                            </span>
                                        </div>
                                        
                                        <div class="p-4">
                                            <div class="grid md:grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <span class="text-sm text-gray-500"><?= $this->localization->t('quotes.price') ?></span>
                                                    <p class="text-xl font-bold text-gray-900"><?= number_format($quote['price'], 2) ?> <?= $this->localization->t('general.currency') ?></p>
                                                </div>
                                                
                                                <div>
                                                    <span class="text-sm text-gray-500"><?= $this->localization->t('quotes.delivery_time') ?></span>
                                                    <p class="font-medium">
                                                        <?= $quote['estimated_delivery_days'] ?> <?= $this->localization->t('quotes.days') ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <?php if (!empty($quote['message'])): ?>
                                                <div class="mb-4">
                                                    <span class="text-sm text-gray-500"><?= $this->localization->t('quotes.vendor_message') ?></span>
                                                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($quote['message'])) ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($quote['status'] === 'offered'): ?>
                                                <form action="/quotes/accept" method="post">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="quote_id" value="<?= $quote['id'] ?>">
                                                    <?php if (!$this->auth->isLoggedIn() && !empty($email)): ?>
                                                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                                    <?php endif; ?>
                                                    
                                                    <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                        <?= $this->localization->t('quotes.accept_quote') ?>
                                                    </button>
                                                </form>
                                            <?php elseif ($quote['status'] === 'accepted'): ?>
                                                <div class="flex justify-end">
                                                    <a href="<?= $this->auth->isLoggedIn() ? 
                                                        "/orders/place?quote_id={$quote['id']}" : 
                                                        "/orders/guest-place?quote_id={$quote['id']}&email=" . urlencode($email) ?>" 
                                                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                        <?= $this->localization->t('quotes.proceed_to_order') ?>
                                                        <svg class="h-5 w-5 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    <?= $this->localization->t('quotes.no_quotes_yet') ?>
                                </h3>
                                <p class="text-gray-600 mb-4">
                                    <?= $this->localization->t('quotes.vendor_reviewing') ?>
                                </p>
                                <div class="text-sm text-gray-500">
                                    <p><?= $this->localization->t('quotes.check_back_later') ?></p>
                                    <p><?= $this->localization->t('quotes.notification_sent') ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation links -->
    <div class="mt-6 flex justify-between">
        <a href="<?= $this->auth->isLoggedIn() ? '/quotes/history' : '/services' ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md transition-colors">
            <svg class="h-5 w-5 <?= $marginEnd ?>-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <?= $this->auth->isLoggedIn() ? $this->localization->t('quotes.back_to_history') : $this->localization->t('nav.browse_services') ?>
        </a>
        
        <?php if ($hasQuotes && count($quotes) > 1): ?>
            <a href="/quotes/compare/<?= $quoteRequest['id'] ?><?= $email ? '?email=' . urlencode($email) : '' ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <?= $this->localization->t('quotes.compare_quotes') ?>
                <svg class="h-5 w-5 <?= $marginStart ?>-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
