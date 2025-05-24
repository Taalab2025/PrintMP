<?php
/**
 * Vendor Subscription Management View
 * File path: views/pages/vendor-dashboard/subscription.php
 * Enhanced subscription management with plan comparison and upgrade workflow
 */

$language = $this->localization->getCurrentLanguage();
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';

// Get subscription data passed from controller
$subscriptionStatus = $subscriptionStatus ?? [];
$plans = $plans ?? [];
$currentPlan = $subscriptionStatus['plan'] ?? 'free';
$subscriptionHistory = $subscriptionHistory ?? [];
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="<?= $textAlign ?>">
        <h1 class="text-2xl font-bold text-gray-900">
            <?= $this->localization->t('vendor.subscription_management') ?>
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            <?= $this->localization->t('vendor.manage_subscription_desc') ?>
        </p>
    </div>

    <!-- Current Plan Status -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <?= $this->localization->t('vendor.current_plan') ?>
            </h2>

            <?php
            $statusColor = $subscriptionStatus['status'] === 'active' ? 'green' : 'red';
            $statusText = $this->localization->t('vendor.status_' . $subscriptionStatus['status']);
            ?>
            <span class="px-3 py-1 text-sm font-medium rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800">
                <?= $statusText ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Plan Info -->
            <div class="<?= $textAlign ?>">
                <h3 class="text-xl font-bold text-blue-600 mb-2">
                    <?= $plans[$currentPlan]['name_' . $language] ?? ucfirst($currentPlan) ?>
                </h3>
                <p class="text-gray-600 text-sm mb-4">
                    <?= $plans[$currentPlan]['description_' . $language] ?? '' ?>
                </p>

                <?php if ($currentPlan !== 'free'): ?>
                    <div class="text-2xl font-bold text-gray-900">
                        <?= number_format($plans[$currentPlan]['price'] ?? 0) ?>
                        <span class="text-sm font-normal text-gray-500">
                            <?= $this->localization->t('general.currency') ?>/<?= $this->localization->t('vendor.month') ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="text-2xl font-bold text-green-600">
                        <?= $this->localization->t('vendor.free') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quote Usage -->
            <div class="<?= $textAlign ?>">
                <h4 class="text-sm font-medium text-gray-500 mb-2">
                    <?= $this->localization->t('vendor.quote_usage') ?>
                </h4>

                <?php if ($subscriptionStatus['monthly_quote_limit'] == -1): ?>
                    <div class="text-lg font-semibold text-gray-900">
                        <?= $subscriptionStatus['quote_count_used'] ?>
                        <span class="text-sm font-normal text-gray-500">
                            / <?= $this->localization->t('vendor.unlimited') ?>
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 20%"></div>
                    </div>
                <?php else: ?>
                    <div class="text-lg font-semibold text-gray-900">
                        <?= $subscriptionStatus['quote_count_used'] ?> / <?= $subscriptionStatus['monthly_quote_limit'] ?>
                    </div>

                    <?php
                    $usagePercentage = $subscriptionStatus['monthly_quote_limit'] > 0
                        ? ($subscriptionStatus['quote_count_used'] / $subscriptionStatus['monthly_quote_limit']) * 100
                        : 0;
                    $progressColor = $usagePercentage >= 80 ? 'red' : ($usagePercentage >= 60 ? 'yellow' : 'blue');
                    ?>

                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-<?= $progressColor ?>-600 h-2 rounded-full transition-all duration-300"
                             style="width: <?= min(100, $usagePercentage) ?>%"></div>
                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                        <?= $subscriptionStatus['quotes_remaining'] ?> <?= $this->localization->t('vendor.quotes_remaining') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Plan Dates -->
            <div class="<?= $textAlign ?>">
                <h4 class="text-sm font-medium text-gray-500 mb-2">
                    <?= $this->localization->t('vendor.plan_period') ?>
                </h4>

                <div class="space-y-1">
                    <p class="text-sm text-gray-900">
                        <span class="font-medium"><?= $this->localization->t('vendor.start_date') ?>:</span>
                        <?= date('M d, Y', strtotime($subscriptionStatus['start_date'])) ?>
                    </p>

                    <?php if ($subscriptionStatus['end_date']): ?>
                        <p class="text-sm text-gray-900">
                            <span class="font-medium"><?= $this->localization->t('vendor.end_date') ?>:</span>
                            <?= date('M d, Y', strtotime($subscriptionStatus['end_date'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">
                            <?= $this->localization->t('vendor.no_expiry') ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Warning for Free Plan Limit -->
        <?php if ($currentPlan === 'free' && $subscriptionStatus['quotes_remaining'] <= 2): ?>
            <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 <?= $marginEnd ?>-2"></i>
                    <p class="text-sm text-orange-700">
                        <?= $this->localization->t('vendor.quote_limit_warning') ?>
                        <a href="#plans" class="font-medium underline hover:no-underline">
                            <?= $this->localization->t('vendor.upgrade_now') ?>
                        </a>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Available Plans -->
    <div id="plans" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 <?= $textAlign ?>">
            <?= $this->localization->t('vendor.available_plans') ?>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($plans as $planKey => $plan): ?>
                <div class="border rounded-lg p-6 <?= $planKey === $currentPlan ? 'border-blue-500 bg-blue-50' : 'border-gray-200' ?> relative">

                    <?php if ($planKey === $currentPlan): ?>
                        <div class="absolute top-0 <?= $isRtl ? 'left-4' : 'right-4' ?> transform -translate-y-1/2">
                            <span class="bg-blue-600 text-white text-xs font-medium px-2 py-1 rounded-full">
                                <?= $this->localization->t('vendor.current_plan') ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ($planKey === 'premium'): ?>
                        <div class="absolute top-0 <?= $isRtl ? 'right-4' : 'left-4' ?> transform -translate-y-1/2">
                            <span class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-medium px-2 py-1 rounded-full">
                                <?= $this->localization->t('vendor.most_popular') ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="<?= $textAlign ?>">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            <?= $plan['name_' . $language] ?>
                        </h3>

                        <div class="mb-4">
                            <?php if ($plan['price'] > 0): ?>
                                <span class="text-3xl font-bold text-gray-900">
                                    <?= number_format($plan['price']) ?>
                                </span>
                                <span class="text-gray-500">
                                    <?= $this->localization->t('general.currency') ?>/<?= $this->localization->t('vendor.month') ?>
                                </span>
                            <?php else: ?>
                                <span class="text-3xl font-bold text-green-600">
                                    <?= $this->localization->t('vendor.free') ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <p class="text-sm text-gray-600 mb-6">
                            <?= $plan['description_' . $language] ?>
                        </p>

                        <!-- Features List -->
                        <ul class="space-y-2 mb-6 text-sm">
                            <li class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?>">
                                <i class="fas fa-check text-green-500 <?= $marginEnd ?>-2"></i>
                                <?php if ($plan['quote_limit'] == -1): ?>
                                    <?= $this->localization->t('vendor.unlimited_quotes') ?>
                                <?php else: ?>
                                    <?= $plan['quote_limit'] ?> <?= $this->localization->t('vendor.quotes_per_month') ?>
                                <?php endif; ?>
                            </li>

                            <?php foreach ($plan['features'] as $feature => $enabled): ?>
                                <?php if ($enabled): ?>
                                    <li class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?>">
                                        <i class="fas fa-check text-green-500 <?= $marginEnd ?>-2"></i>
                                        <?= $this->localization->t('vendor.feature_' . $feature) ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Action Button -->
                        <?php if ($planKey === $currentPlan): ?>
                            <button disabled
                                    class="w-full bg-gray-200 text-gray-500 py-2 px-4 rounded-md cursor-not-allowed">
                                <?= $this->localization->t('vendor.current_plan') ?>
                            </button>
                        <?php elseif ($planKey === 'free'): ?>
                            <form method="POST" action="/vendor/subscription/downgrade">
                                <?= $this->session->getCSRFTokenField() ?>
                                <input type="hidden" name="plan" value="free">
                                <button type="submit"
                                        class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition-colors"
                                        onclick="return confirm('<?= $this->localization->t('vendor.confirm_downgrade') ?>')">
                                    <?= $this->localization->t('vendor.downgrade') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/vendor/subscription/upgrade">
                                <?= $this->session->getCSRFTokenField() ?>
                                <input type="hidden" name="plan" value="<?= $planKey ?>">
                                <button type="submit"
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                    <?= $this->localization->t('vendor.upgrade_to_plan') ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Subscription History -->
    <?php if (!empty($subscriptionHistory)): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6 <?= $textAlign ?>">
                <?= $this->localization->t('vendor.subscription_history') ?>
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('vendor.date') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('vendor.old_plan') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('vendor.new_plan') ?>
                            </th>
                            <th class="px-6 py-3 <?= $textAlign ?> text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?= $this->localization->t('vendor.reason') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($subscriptionHistory as $history): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 <?= $textAlign ?>">
                                    <?= date('M d, Y', strtotime($history['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 <?= $textAlign ?>">
                                    <?= ucfirst($history['old_plan']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 <?= $textAlign ?>">
                                    <?= ucfirst($history['new_plan']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 <?= $textAlign ?>">
                                    <?= htmlspecialchars($history['reason'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- FAQ Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 <?= $textAlign ?>">
            <?= $this->localization->t('vendor.subscription_faq') ?>
        </h2>

        <div class="space-y-4">
            <div class="border-b border-gray-200 pb-4">
                <button class="faq-toggle flex items-center justify-between w-full <?= $textAlign ?> text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors"
                        data-target="faq1">
                    <span><?= $this->localization->t('vendor.faq_billing_question') ?></span>
                    <i class="fas fa-chevron-down transform transition-transform duration-200"></i>
                </button>
                <div id="faq1" class="faq-content hidden mt-2 text-sm text-gray-600 <?= $textAlign ?>">
                    <?= $this->localization->t('vendor.faq_billing_answer') ?>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-4">
                <button class="faq-toggle flex items-center justify-between w-full <?= $textAlign ?> text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors"
                        data-target="faq2">
                    <span><?= $this->localization->t('vendor.faq_upgrade_question') ?></span>
                    <i class="fas fa-chevron-down transform transition-transform duration-200"></i>
                </button>
                <div id="faq2" class="faq-content hidden mt-2 text-sm text-gray-600 <?= $textAlign ?>">
                    <?= $this->localization->t('vendor.faq_upgrade_answer') ?>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-4">
                <button class="faq-toggle flex items-center justify-between w-full <?= $textAlign ?> text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors"
                        data-target="faq3">
                    <span><?= $this->localization->t('vendor.faq_cancel_question') ?></span>
                    <i class="fas fa-chevron-down transform transition-transform duration-200"></i>
                </button>
                <div id="faq3" class="faq-content hidden mt-2 text-sm text-gray-600 <?= $textAlign ?>">
                    <?= $this->localization->t('vendor.faq_cancel_answer') ?>
                </div>
            </div>

            <div>
                <button class="faq-toggle flex items-center justify-between w-full <?= $textAlign ?> text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors"
                        data-target="faq4">
                    <span><?= $this->localization->t('vendor.faq_quotes_question') ?></span>
                    <i class="fas fa-chevron-down transform transition-transform duration-200"></i>
                </button>
                <div id="faq4" class="faq-content hidden mt-2 text-sm text-gray-600 <?= $textAlign ?>">
                    <?= $this->localization->t('vendor.faq_quotes_answer') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Contact -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?>">
            <div class="flex-shrink-0">
                <i class="fas fa-headset text-blue-600 text-2xl"></i>
            </div>
            <div class="<?= $marginStart ?>-4 <?= $textAlign ?>">
                <h3 class="text-lg font-medium text-blue-900">
                    <?= $this->localization->t('vendor.need_help') ?>
                </h3>
                <p class="mt-1 text-blue-700">
                    <?= $this->localization->t('vendor.subscription_support_desc') ?>
                </p>
                <div class="mt-3">
                    <a href="/contact"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors">
                        <?= $this->localization->t('vendor.contact_support') ?>
                        <i class="fas fa-arrow-<?= $isRtl ? 'left' : 'right' ?> <?= $marginStart ?>-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    const faqToggles = document.querySelectorAll('.faq-toggle');

    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const content = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        });
    });

    // Show upgrade confirmation
    const upgradeButtons = document.querySelectorAll('form[action*="upgrade"] button');
    upgradeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const planName = this.form.querySelector('input[name="plan"]').value;
            const confirmMessage = '<?= $this->localization->t('vendor.confirm_upgrade') ?>'.replace('%s', planName);

            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    });
});
</script>
