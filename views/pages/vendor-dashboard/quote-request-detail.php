<?php
/**
 * Vendor Quote Request Detail
 * File path: views/pages/vendor-dashboard/quote-request-detail.php
 * 
 * Shows details of a quote request and allows vendors to respond
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
$csrfToken = $this->session->generateCsrfToken();
$errors = $this->session->getFlash('errors') ?? [];
$oldInput = $this->session->getFlash('old_input') ?? [];

$title = $this->localization->t('vendor.quote_request_detail');
?>

<?php include 'views/layouts/vendor.php'; ?>

<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="flex items-center py-4 text-sm text-gray-600 <?= $textAlign ?>">
        <a href="/vendor/dashboard" class="hover:text-primary-600"><?= $this->localization->t('vendor.dashboard') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="/vendor/quote-requests" class="hover:text-primary-600"><?= $this->localization->t('vendor.quote_requests') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-500"><?= $this->localization->t('vendor.request') ?> #<?= $quoteRequest['id'] ?></span>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Request Header -->
        <div class="px-6 py-4 bg-gray-50 border-b <?= $textAlign ?>">
            <div class="flex flex-wrap items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold"><?= $this->localization->t('vendor.quote_request') ?> #<?= $quoteRequest['id'] ?></h1>
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
                                <p class="text-gray-700 text-sm mb-3"><?= mb_substr($service["description_{$language}"], 0, 150) ?>...</p>
                                <a href="/vendor/services/edit/<?= $service['id'] ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium inline-flex items-center">
                                    <?= $this->localization->t('vendor.view_service') ?>
                                    <svg class="h-4 w-4 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Information -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-3">
                            <?= $this->localization->t('vendor.customer_information') ?>
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="mb-3">
                                <span class="font-medium"><?= $this->localization->t('quotes.contact_name') ?>:</span>
                                <span class="text-gray-700 <?= $marginStart ?>-1"><?= htmlspecialchars($quoteRequest['contact_name']) ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="font-medium"><?= $this->localization->t('quotes.contact_email') ?>:</span>
                                <span class="text-gray-700 <?= $marginStart ?>-1"><?= htmlspecialchars($quoteRequest['contact_email']) ?></span>
                            </div>
                            <?php if (!empty($quoteRequest['contact_phone'])): ?>
                            <div class="mb-3">
                                <span class="font-medium"><?= $this->localization->t('quotes.contact_phone') ?>:</span>
                                <span class="text-gray-700 <?= $marginStart ?>-1"><?= htmlspecialchars($quoteRequest['contact_phone']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($quoteRequest['delivery_address'])): ?>
                            <div>
                                <span class="font-medium"><?= $this->localization->t('quotes.delivery_address') ?>:</span>
                                <div class="text-gray-700 mt-1"><?= nl2br(htmlspecialchars($quoteRequest['delivery_address'])) ?></div>
                            </div>
                            <?php endif; ?>
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
                                    <li class="flex items-center justify-between">
                                        <div class="flex items-center">
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
                                        </div>
                                        
                                        <a href="/quote-files/download/<?= $file['id'] ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                            <?= $this->localization->t('vendor.download') ?>
                                        </a>
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
                
                <!-- Right column: Quote Form -->
                <div>
                    <?php if ($isFreemiumLimitReached && !$hasActiveSubscription && !$hasQuoted): ?>
                        <!-- Subscription Limit Reached -->
                        <div class="border border-red-200 rounded-lg bg-red-50 p-6 mb-6">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-red-100 p-2 <?= $marginEnd ?>-3">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-red-800">
                                    <?= $this->localization->t('vendor.subscription_limit_reached') ?>
                                </h3>
                            </div>
                            
                            <p class="text-red-700 mb-4">
                                <?= $this->localization->t('vendor.freemium_limit_explanation') ?>
                            </p>
                            
                            <a href="/vendor/subscription" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <?= $this->localization->t('vendor.upgrade_subscription') ?>
                                <svg class="h-5 w-5 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    <?php elseif ($hasQuoted && $vendorQuote['status'] === 'offered'): ?>
                        <!-- Already Quoted - Edit Form -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-blue-100 p-2 <?= $marginEnd ?>-3">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-blue-800">
                                        <?= $this->localization->t('vendor.edit_quote') ?>
                                    </h3>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="mb-4">
                                    <p class="text-gray-600">
                                        <?= $this->localization->t('vendor.quoted_on', ['date' => date('F j, Y', strtotime($vendorQuote['created_at']))]) ?>
                                    </p>
                                    
                                    <p class="text-gray-600">
                                        <?= $this->localization->t('vendor.valid_until', ['date' => date('F j, Y', strtotime($vendorQuote['valid_until']))]) ?>
                                    </p>
                                </div>
                                
                                <form action="/quotes/vendor-response" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="quote_request_id" value="<?= $quoteRequest['id'] ?>">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="form-group">
                                            <label for="price" class="block font-medium mb-2">
                                                <?= $this->localization->t('vendor.price') ?> <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 <?= $isRtl ? 'right' : 'left' ?>-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500"><?= $this->localization->t('general.currency_symbol') ?></span>
                                                </div>
                                                <input 
                                                    type="number" 
                                                    id="price" 
                                                    name="price" 
                                                    step="0.01" 
                                                    min="0" 
                                                    value="<?= $oldInput['price'] ?? $vendorQuote['price'] ?>" 
                                                    class="w-full <?= $isRtl ? 'pr-10' : 'pl-10' ?> px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['price']) ? 'border-red-500' : 'border-gray-300' ?>"
                                                    required
                                                >
                                            </div>
                                            <?php if (isset($errors['price'])): ?>
                                                <p class="text-red-500 text-sm mt-1"><?= $errors['price'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="estimated_delivery_days" class="block font-medium mb-2">
                                                <?= $this->localization->t('vendor.delivery_days') ?> <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input 
                                                    type="number" 
                                                    id="estimated_delivery_days" 
                                                    name="estimated_delivery_days" 
                                                    min="1" 
                                                    value="<?= $oldInput['estimated_delivery_days'] ?? $vendorQuote['estimated_delivery_days'] ?>" 
                                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['estimated_delivery_days']) ? 'border-red-500' : 'border-gray-300' ?>"
                                                    required
                                                >
                                                <div class="absolute inset-y-0 <?= $isRtl ? 'left' : 'right' ?>-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500"><?= $this->localization->t('quotes.days') ?></span>
                                                </div>
                                            </div>
                                            <?php if (isset($errors['estimated_delivery_days'])): ?>
                                                <p class="text-red-500 text-sm mt-1"><?= $errors['estimated_delivery_days'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-6">
                                        <label for="message" class="block font-medium mb-2">
                                            <?= $this->localization->t('vendor.message_to_customer') ?>
                                        </label>
                                        <textarea 
                                            id="message" 
                                            name="message" 
                                            rows="4" 
                                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300"
                                        ><?= $oldInput['message'] ?? $vendorQuote['message'] ?></textarea>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <?= $this->localization->t('vendor.message_tip') ?>
                                        </p>
                                    </div>
                                    
                                    <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        <?= $this->localization->t('vendor.update_quote') ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php elseif ($hasQuoted && $vendorQuote['status'] !== 'offered'): ?>
                        <!-- Quote has been accepted or declined -->
                        <div class="border rounded-lg overflow-hidden mb-6">
                            <div class="bg-<?= $vendorQuote['status'] === 'accepted' ? 'green' : 'gray' ?>-50 px-6 py-4 border-b border-<?= $vendorQuote['status'] === 'accepted' ? 'green' : 'gray' ?>-100">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-<?= $vendorQuote['status'] === 'accepted' ? 'green' : 'gray' ?>-100 p-2 <?= $marginEnd ?>-3">
                                        <?php if ($vendorQuote['status'] === 'accepted'): ?>
                                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="text-lg font-semibold text-<?= $vendorQuote['status'] === 'accepted' ? 'green' : 'gray' ?>-800">
                                        <?= $this->localization->t('vendor.quote_' . $vendorQuote['status']) ?>
                                    </h3>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.price') ?></h4>
                                        <p class="text-xl font-bold text-gray-900">
                                            <?= $this->localization->t('general.currency_symbol') ?><?= number_format($vendorQuote['price'], 2) ?>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.delivery_time') ?></h4>
                                        <p class="font-medium">
                                            <?= $vendorQuote['estimated_delivery_days'] ?> <?= $this->localization->t('quotes.days') ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if (!empty($vendorQuote['message'])): ?>
                                    <div class="mb-4">
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.vendor_message') ?></h4>
                                        <p class="text-gray-700"><?= nl2br(htmlspecialchars($vendorQuote['message'])) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($vendorQuote['status'] === 'accepted'): ?>
                                    <div class="bg-green-50 p-4 rounded-md mt-4">
                                        <p class="text-green-700">
                                            <?= $this->localization->t('vendor.quote_accepted_instructions') ?>
                                        </p>
                                    </div>
                                    
                                    <a href="/vendor/orders" class="inline-flex items-center mt-4 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        <?= $this->localization->t('vendor.view_orders') ?>
                                        <svg class="h-5 w-5 <?= $marginStart ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif (!$hasQuoted && !$isFreemiumLimitReached || $hasActiveSubscription): ?>
                        <!-- Submit New Quote Form -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                            <div class="bg-primary-50 px-6 py-4 border-b border-primary-100">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-primary-100 p-2 <?= $marginEnd ?>-3">
                                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-primary-800">
                                        <?= $this->localization->t('vendor.submit_quote') ?>
                                    </h3>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <form action="/quotes/vendor-response" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="quote_request_id" value="<?= $quoteRequest['id'] ?>">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="form-group">
                                            <label for="price" class="block font-medium mb-2">
                                                <?= $this->localization->t('vendor.price') ?> <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 <?= $isRtl ? 'right' : 'left' ?>-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500"><?= $this->localization->t('general.currency_symbol') ?></span>
                                                </div>
                                                <input 
                                                    type="number" 
                                                    id="price" 
                                                    name="price" 
                                                    step="0.01" 
                                                    min="0" 
                                                    value="<?= $oldInput['price'] ?? '' ?>" 
                                                    class="w-full <?= $isRtl ? 'pr-10' : 'pl-10' ?> px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['price']) ? 'border-red-500' : 'border-gray-300' ?>"
                                                    required
                                                >
                                            </div>
                                            <?php if (isset($errors['price'])): ?>
                                                <p class="text-red-500 text-sm mt-1"><?= $errors['price'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="estimated_delivery_days" class="block font-medium mb-2">
                                                <?= $this->localization->t('vendor.delivery_days') ?> <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input 
                                                    type="number" 
                                                    id="estimated_delivery_days" 
                                                    name="estimated_delivery_days" 
                                                    min="1" 
                                                    value="<?= $oldInput['estimated_delivery_days'] ?? '' ?>" 
                                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['estimated_delivery_days']) ? 'border-red-500' : 'border-gray-300' ?>"
                                                    required
                                                >
                                                <div class="absolute inset-y-0 <?= $isRtl ? 'left' : 'right' ?>-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500"><?= $this->localization->t('quotes.days') ?></span>
                                                </div>
                                            </div>
                                            <?php if (isset($errors['estimated_delivery_days'])): ?>
                                                <p class="text-red-500 text-sm mt-1"><?= $errors['estimated_delivery_days'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-6">
                                        <label for="message" class="block font-medium mb-2">
                                            <?= $this->localization->t('vendor.message_to_customer') ?>
                                        </label>
                                        <textarea 
                                            id="message" 
                                            name="message" 
                                            rows="4" 
                                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300"
                                        ><?= $oldInput['message'] ?? '' ?></textarea>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <?= $this->localization->t('vendor.message_tip') ?>
                                        </p>
                                    </div>
                                    
                                    <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        <?= $this->localization->t('vendor.send_quote') ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Guidelines Box -->
                    <div class="border border-gray-200 rounded-lg p-5 bg-yellow-50">
                        <h3 class="text-lg font-semibold mb-3"><?= $this->localization->t('vendor.quote_guidelines') ?></h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 <?= $marginEnd ?>-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><?= $this->localization->t('vendor.guideline_1') ?></span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 <?= $marginEnd ?>-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><?= $this->localization->t('vendor.guideline_2') ?></span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 <?= $marginEnd ?>-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><?= $this->localization->t('vendor.guideline_3') ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
