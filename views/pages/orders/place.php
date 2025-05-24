<?php
/**
 * Order Placement View
 * File path: views/pages/orders/place.php
 * Session: 7 - Quote Comparison & Order Placement
 */
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="<?= $isRtl ? 'text-right' : 'text-left' ?> mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= $this->localization->t('orders.place_order') ?>
        </h1>
        <p class="mt-2 text-gray-600">
            <?= $this->localization->t('orders.place_order_subtitle') ?>
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->hasFlash('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md" role="alert">
        <div class="flex">
            <i class="fas fa-check-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
            <span><?= $this->session->getFlash('success') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($this->session->hasFlash('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md" role="alert">
        <div class="flex">
            <i class="fas fa-exclamation-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
            <span><?= $this->session->getFlash('error') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-lg p-6 sticky top-4">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.order_summary') ?>
                </h2>

                <!-- Service Details -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">
                        <?= $this->localization->t('orders.service') ?>
                    </h3>
                    <p class="text-sm text-gray-600">
                        <?= htmlspecialchars($service["title_$currentLanguage"]) ?>
                    </p>
                </div>

                <!-- Vendor Details -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">
                        <?= $this->localization->t('orders.vendor') ?>
                    </h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <?php if ($vendor['logo']): ?>
                                <img class="h-8 w-8 rounded-full" src="<?= htmlspecialchars($vendor['logo']) ?>" alt="<?= htmlspecialchars($vendor["company_name_$currentLanguage"]) ?>">
                            <?php else: ?>
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-store text-gray-600 text-sm"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                            <p class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($vendor["company_name_$currentLanguage"]) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quote Details -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">
                        <?= $this->localization->t('orders.quote_details') ?>
                    </h3>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"><?= $this->localization->t('orders.price') ?>:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <?= number_format($quote['price'], 2) ?> <?= $this->localization->t('general.currency') ?>
                            </span>
                        </div>

                        <?php if ($quote['estimated_delivery_days']): ?>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"><?= $this->localization->t('orders.delivery_time') ?>:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <?= $quote['estimated_delivery_days'] ?> <?= $this->localization->t('general.days') ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($quote['message']): ?>
                    <div class="mt-3 p-3 bg-gray-50 rounded-md">
                        <p class="text-xs text-gray-600 mb-1"><?= $this->localization->t('orders.vendor_notes') ?>:</p>
                        <p class="text-sm text-gray-700"><?= htmlspecialchars($quote['message']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center">
                    <span class="text-base font-medium text-gray-900"><?= $this->localization->t('orders.total') ?>:</span>
                    <span class="text-xl font-bold text-gray-900">
                        <?= number_format($quote['price'], 2) ?> <?= $this->localization->t('general.currency') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="/orders/process" method="POST" id="orderForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="quote_id" value="<?= $quote['id'] ?>">

                    <!-- Contact Information -->
                    <div class="mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            <?= $this->localization->t('orders.contact_information') ?>
                        </h2>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Full Name -->
                            <div class="sm:col-span-2">
                                <label for="contact_name" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                                    <?= $this->localization->t('orders.full_name') ?> <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="contact_name"
                                       id="contact_name"
                                       value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                       required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                                    <?= $this->localization->t('orders.email') ?> <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       name="contact_email"
                                       id="contact_email"
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                       required>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                                    <?= $this->localization->t('orders.phone') ?> <span class="text-red-500">*</span>
                                </label>
                                <input type="tel"
                                       name="contact_phone"
                                       id="contact_phone"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            <?= $this->localization->t('orders.delivery_information') ?>
                        </h2>

                        <!-- Delivery Address -->
                        <div>
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                                <?= $this->localization->t('orders.delivery_address') ?> <span class="text-red-500">*</span>
                            </label>
                            <textarea name="delivery_address"
                                      id="delivery_address"
                                      rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                      placeholder="<?= $this->localization->t('orders.delivery_address_placeholder') ?>"
                                      required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                <?= $this->localization->t('orders.delivery_address_note') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            <?= $this->localization->t('orders.additional_notes') ?>
                        </h2>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                                <?= $this->localization->t('orders.special_instructions') ?>
                            </label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                      placeholder="<?= $this->localization->t('orders.special_instructions_placeholder') ?>"></textarea>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-8">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms"
                                       name="terms"
                                       type="checkbox"
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                       required>
                            </div>
                            <div class="<?= $isRtl ? 'mr-3' : 'ml-3' ?> text-sm">
                                <label for="terms" class="text-gray-700">
                                    <?= $this->localization->t('orders.terms_agreement') ?>
                                    <a href="/terms" class="text-blue-600 hover:text-blue-500" target="_blank">
                                        <?= $this->localization->t('orders.terms_and_conditions') ?>
                                    </a>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex <?= $isRtl ? 'flex-row-reverse' : 'flex-row' ?> justify-between items-center pt-6 border-t border-gray-200">
                        <button type="button"
                                onclick="window.history.back()"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left <?= $isRtl ? 'ml-2 fa-flip-horizontal' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('general.back') ?>
                        </button>

                        <button type="submit"
                                class="bg-blue-600 py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-shopping-cart <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.place_order') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const submitButton = e.target.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin <?= $isRtl ? "ml-2" : "mr-2" ?>"></i> <?= $this->localization->t("orders.processing") ?>';
});
</script>
