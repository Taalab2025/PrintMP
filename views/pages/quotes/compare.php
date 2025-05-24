<?php
/**
 * Quote Comparison
 * File path: views/pages/quotes/compare.php
 *
 * Allows users to compare multiple quotes from different vendors
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
$csrfToken = $this->session->generateCsrfToken();

$title = $this->localization->t('quotes.compare_quotes');
$email = isset($_GET['email']) ? $_GET['email'] : '';
$hasQuotes = !empty($quotes);
$offeredQuotes = array_filter($quotes, function($quote) {
    return $quote['status'] === 'offered';
});
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
        <a href="/quotes/track/<?= $quoteRequest['id'] ?><?= $email ? '?email=' . urlencode($email) : '' ?>" class="hover:text-primary-600">
            <?= $this->localization->t('quotes.request') ?> #<?= $quoteRequest['id'] ?>
        </a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-500"><?= $this->localization->t('quotes.compare') ?></span>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Compare Header -->
        <div class="px-6 py-4 bg-gray-50 border-b <?= $textAlign ?>">
            <div class="flex flex-wrap items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold"><?= $this->localization->t('quotes.compare_quotes') ?></h1>
                    <p class="text-gray-600"><?= $this->localization->t('quotes.for_service', ['service' => $service["title_{$language}"]]) ?></p>
                </div>

                <div class="mt-2 md:mt-0">
                    <a href="/quotes/track/<?= $quoteRequest['id'] ?><?= $email ? '?email=' . urlencode($email) : '' ?>" class="inline-flex items-center text-primary-600 hover:text-primary-700">
                        <svg class="h-5 w-5 <?= $marginEnd ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <?= $this->localization->t('quotes.back_to_request') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Compare Table -->
        <div class="p-6">
            <?php if ($hasQuotes): ?>
                <!-- Desktop Comparison Table (hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 border-b border-gray-200 text-left" style="width: 25%;">
                                    <?= $this->localization->t('quotes.comparison_criteria') ?>
                                </th>

                                <?php foreach ($quotes as $index => $quote): ?>
                                    <th class="p-4 border-b border-gray-200 text-center" style="width: <?= (75 / count($quotes)) ?>%;">
                                        <div class="flex flex-col items-center">
                                            <?php if (isset($quote['logo']) && $quote['logo']): ?>
                                                <img src="<?= $quote['logo'] ?>" alt="<?= $quote["company_name_{$language}"] ?>" class="w-10 h-10 rounded-full mb-2">
                                            <?php endif; ?>
                                            <span class="font-medium"><?= $quote["company_name_{$language}"] ?></span>

                                            <?php if (isset($quote['rating'])): ?>
                                                <div class="flex items-center mt-1">
                                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-600 ml-1"><?= number_format($quote['rating'], 1) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Price Row -->
                            <tr>
                                <td class="p-4 border-b border-gray-200 font-medium">
                                    <?= $this->localization->t('quotes.price') ?>
                                </td>

                                <?php
                                // Find lowest price
                                $prices = array_column($quotes, 'price');
                                $lowestPrice = min($prices);

                                foreach ($quotes as $quote):
                                ?>
                                    <td class="p-4 border-b border-gray-200 text-center">
                                        <div class="<?= $quote['price'] === $lowestPrice ? 'text-green-600 font-bold' : '' ?>">
                                            <?= number_format($quote['price'], 2) ?> <?= $this->localization->t('general.currency') ?>
                                        </div>

                                        <?php if ($quote['price'] === $lowestPrice): ?>
                                            <div class="text-xs text-green-600 mt-1">
                                                <?= $this->localization->t('quotes.best_price') ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Delivery Time Row -->
                            <tr>
                                <td class="p-4 border-b border-gray-200 font-medium">
                                    <?= $this->localization->t('quotes.delivery_time') ?>
                                </td>

                                <?php
                                // Find fastest delivery
                                $deliveryTimes = array_column($quotes, 'estimated_delivery_days');
                                $fastestDelivery = min($deliveryTimes);

                                foreach ($quotes as $quote):
                                ?>
                                    <td class="p-4 border-b border-gray-200 text-center">
                                        <div class="<?= $quote['estimated_delivery_days'] === $fastestDelivery ? 'text-green-600 font-bold' : '' ?>">
                                            <?= $quote['estimated_delivery_days'] ?> <?= $this->localization->t('quotes.days') ?>
                                        </div>

                                        <?php if ($quote['estimated_delivery_days'] === $fastestDelivery): ?>
                                            <div class="text-xs text-green-600 mt-1">
                                                <?= $this->localization->t('quotes.fastest_delivery') ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Vendor Message Row -->
                            <tr>
                                <td class="p-4 border-b border-gray-200 font-medium">
                                    <?= $this->localization->t('quotes.vendor_message') ?>
                                </td>

                                <?php foreach ($quotes as $quote): ?>
                                    <td class="p-4 border-b border-gray-200 text-center">
                                        <div class="text-sm text-gray-700">
                                            <?= !empty($quote['message']) ? nl2br(htmlspecialchars($quote['message'])) :
                                                '<span class="text-gray-400">' . $this->localization->t('quotes.no_message') . '</span>' ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Valid Until Row -->
                            <tr>
                                <td class="p-4 border-b border-gray-200 font-medium">
                                    <?= $this->localization->t('quotes.valid_until') ?>
                                </td>

                                <?php foreach ($quotes as $quote): ?>
                                    <td class="p-4 border-b border-gray-200 text-center">
                                        <?php if ($quote['status'] === 'offered'): ?>
                                            <div class="text-sm">
                                                <?= date('M d, Y', strtotime($quote['valid_until'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-sm text-gray-500">
                                                <?= $this->localization->t('quotes.status_' . $quote['status']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Status Row -->
                            <tr>
                                <td class="p-4 border-b border-gray-200 font-medium">
                                    <?= $this->localization->t('quotes.status') ?>
                                </td>

                                <?php foreach ($quotes as $quote): ?>
                                    <td class="p-4 border-b border-gray-200 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
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
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Action Row -->
                            <tr>
                                <td class="p-4 font-medium">
                                    <?= $this->localization->t('quotes.action') ?>
                                </td>

                                <?php foreach ($quotes as $quote): ?>
                                    <td class="p-4 text-center">
                                        <?php if ($quote['status'] === 'offered'): ?>
                                            <form action="/quotes/accept" method="post">
                                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                <input type="hidden" name="quote_id" value="<?= $quote['id'] ?>">
                                                <?php if (!$this->auth->isLoggedIn() && !empty($email)): ?>
                                                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                                <?php endif; ?>

                                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 text-sm">
                                                    <?= $this->localization->t('quotes.accept_quote') ?>
                                                </button>
                                            </form>
                                        <?php elseif ($quote['status'] === 'accepted'): ?>
                                            <a href="<?= $this->auth->isLoggedIn() ?
                                                "/orders/place?quote_id={$quote['id']}" :
                                                "/orders/guest-place?quote_id={$quote['id']}&email=" . urlencode($email) ?>"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm">
                                                <?= $this->localization->t('quotes.proceed_to_order') ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-500 text-sm">
                                                <?= $this->localization->t('quotes.no_action_available') ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Comparison Cards (visible only on mobile) -->
                <div class="md:hidden">
                    <?php if (count($quotes) > 1): ?>
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="<?= $marginEnd ?>-3 pt-1">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-blue-700">
                                <?= $this->localization->t('quotes.swipe_to_compare') ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="flex overflow-x-auto pb-4 space-x-4">
                        <?php foreach ($quotes as $quote): ?>
                            <div class="min-w-[280px] max-w-[280px] border rounded-lg overflow-hidden flex-shrink-0">
                                <div class="bg-gray-50 p-4 border-b">
                                    <div class="flex flex-col items-center mb-2">
                                        <?php if (isset($quote['logo']) && $quote['logo']): ?>
                                            <img src="<?= $quote['logo'] ?>" alt="<?= $quote["company_name_{$language}"] ?>" class="w-12 h-12 rounded-full mb-2">
                                        <?php endif; ?>
                                        <h3 class="font-medium text-center"><?= $quote["company_name_{$language}"] ?></h3>

                                        <?php if (isset($quote['rating'])): ?>
                                            <div class="flex items-center mt-1">
                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-600 ml-1"><?= number_format($quote['rating'], 1) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex justify-center">
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
                                </div>

                                <div class="p-4">
                                    <!-- Price -->
                                    <div class="mb-4">
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.price') ?></h4>
                                        <div class="<?= $quote['price'] === $lowestPrice ? 'text-green-600 font-bold' : 'text-gray-900' ?>">
                                            <?= number_format($quote['price'], 2) ?> <?= $this->localization->t('general.currency') ?>

                                            <?php if ($quote['price'] === $lowestPrice): ?>
                                                <span class="text-xs text-green-600 <?= $marginStart ?>-1">
                                                    <?= $this->localization->t('quotes.best_price') ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Delivery Time -->
                                    <div class="mb-4">
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.delivery_time') ?></h4>
                                        <div class="<?= $quote['estimated_delivery_days'] === $fastestDelivery ? 'text-green-600 font-bold' : 'text-gray-900' ?>">
                                            <?= $quote['estimated_delivery_days'] ?> <?= $this->localization->t('quotes.days') ?>

                                            <?php if ($quote['estimated_delivery_days'] === $fastestDelivery): ?>
                                                <span class="text-xs text-green-600 <?= $marginStart ?>-1">
                                                    <?= $this->localization->t('quotes.fastest_delivery') ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Valid Until -->
                                    <div class="mb-4">
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.valid_until') ?></h4>
                                        <?php if ($quote['status'] === 'offered'): ?>
                                            <div class="text-gray-900">
                                                <?= date('M d, Y', strtotime($quote['valid_until'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-gray-500">
                                                <?= $this->localization->t('quotes.status_' . $quote['status']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Vendor Message -->
                                    <div class="mb-4">
                                        <h4 class="text-sm text-gray-500 mb-1"><?= $this->localization->t('quotes.vendor_message') ?></h4>
                                        <div class="text-gray-700 text-sm">
                                            <?= !empty($quote['message']) ? nl2br(htmlspecialchars($quote['message'])) :
                                                '<span class="text-gray-400">' . $this->localization->t('quotes.no_message') . '</span>' ?>
                                        </div>
                                    </div>

                                    <!-- Action -->
                                    <?php if ($quote['status'] === 'offered'): ?>
                                        <form action="/quotes/accept" method="post">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="quote_id" value="<?= $quote['id'] ?>">
                                            <?php if (!$this->auth->isLoggedIn() && !empty($email)): ?>
                                                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                            <?php endif; ?>

                                            <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 text-sm">
                                                <?= $this->localization->t('quotes.accept_quote') ?>
                                            </button>
                                        </form>
                                    <?php elseif ($quote['status'] === 'accepted'): ?>
                                        <a href="<?= $this->auth->isLoggedIn() ?
                                            "/orders/place?quote_id={$quote['id']}" :
                                            "/orders/guest-place?quote_id={$quote['id']}&email=" . urlencode($email) ?>"
                                            class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm">
                                            <?= $this->localization->t('quotes.proceed_to_order') ?>
                                        </a>
                                    <?php else: ?>
                                        <div class="text-center text-gray-500 text-sm">
                                            <?= $this->localization->t('quotes.no_action_available') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h2 class="text-xl font-medium text-gray-900 mb-2">
                        <?= $this->localization->t('quotes.no_quotes_to_compare') ?>
                    </h2>
                    <p class="text-gray-600 mb-4">
                        <?= $this->localization->t('quotes.waiting_for_responses') ?>
                    </p>
                    <a href="/quotes/track/<?= $quoteRequest['id'] ?><?= $email ? '?email=' . urlencode($email) : '' ?>" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="h-5 w-5 <?= $marginEnd ?>-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <?= $this->localization->t('quotes.back_to_request') ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
