<?php
/**
 * Quote Request Confirmation
 * File path: views/pages/quotes/confirmation.php
 * 
 * Shows confirmation after a quote request is submitted
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';

$title = $this->localization->t('quotes.request_confirmation');
?>

<?php include 'views/layouts/main.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 <?= $textAlign ?>">
            <!-- Success Icon -->
            <div class="flex justify-center mb-6">
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-2xl font-bold mb-4 text-center"><?= $this->localization->t('quotes.thank_you') ?></h1>
            <p class="text-center text-gray-700 mb-6">
                <?= $this->localization->t('quotes.request_received') ?>
            </p>
            
            <!-- Request Summary -->
            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('quotes.request_summary') ?></h3>
                <div class="space-y-2">
                    <div class="flex flex-wrap">
                        <span class="font-medium <?= $marginEnd ?>-2"><?= $this->localization->t('quotes.service') ?>:</span>
                        <span class="text-gray-700"><?= $service["title_{$language}"] ?></span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="font-medium <?= $marginEnd ?>-2"><?= $this->localization->t('quotes.request_id') ?>:</span>
                        <span class="text-gray-700"><?= $quoteRequest['id'] ?></span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="font-medium <?= $marginEnd ?>-2"><?= $this->localization->t('quotes.request_date') ?>:</span>
                        <span class="text-gray-700"><?= date('M d, Y', strtotime($quoteRequest['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('quotes.what_happens_next') ?></h3>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li><?= $this->localization->t('quotes.step1_vendor_notified') ?></li>
                    <li><?= $this->localization->t('quotes.step2_vendor_responds') ?></li>
                    <li><?= $this->localization->t('quotes.step3_receive_quotes') ?></li>
                    <li><?= $this->localization->t('quotes.step4_compare_choose') ?></li>
                </ul>
            </div>
            
            <!-- Email Notification -->
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="<?= $marginEnd ?>-3 pt-1">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700">
                            <?= $this->localization->t('quotes.email_notification', ['email' => $quoteRequest['contact_email']]) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Tracking Link -->
            <div class="mb-6">
                <p class="mb-2"><?= $this->localization->t('quotes.track_request') ?>:</p>
                <div class="flex">
                    <input 
                        type="text" 
                        value="<?= $_SERVER['HTTP_HOST'] ?>/quotes/track/<?= $quoteRequest['id'] ?>?email=<?= urlencode($quoteRequest['contact_email']) ?>" 
                        id="tracking-link" 
                        class="flex-grow px-4 py-2 border rounded-l-md focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300"
                        readonly
                    >
                    <button 
                        type="button" 
                        onclick="copyTrackingLink()" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-r-md"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
                <p id="copy-success" class="text-green-600 text-sm mt-1 hidden">
                    <?= $this->localization->t('quotes.link_copied') ?>
                </p>
            </div>
            
            <!-- Create Account Suggestion -->
            <?php if (!$this->auth->isLoggedIn()): ?>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('quotes.stay_organized') ?></h3>
                <p class="text-gray-700 mb-3">
                    <?= $this->localization->t('quotes.create_account_suggestion') ?>
                </p>
                <a 
                    href="/register?email=<?= urlencode($quoteRequest['contact_email']) ?>" 
                    class="inline-block px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    <?= $this->localization->t('auth.create_account') ?>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="flex flex-wrap justify-between mt-6">
                <a href="/services" class="mb-3 md:mb-0 inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md transition-colors">
                    <svg class="h-5 w-5 <?= $marginEnd ?>-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <?= $this->localization->t('nav.browse_services') ?>
                </a>
                
                <a href="/quotes/track/<?= $quoteRequest['id'] ?>?email=<?= urlencode($quoteRequest['contact_email']) ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <?= $this->localization->t('quotes.track_your_request') ?>
                    <svg class="h-5 w-5 <?= $marginStart ?>-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyTrackingLink() {
    const copyText = document.getElementById("tracking-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    
    document.execCommand("copy");
    
    // Show success message
    const copySuccess = document.getElementById("copy-success");
    copySuccess.classList.remove("hidden");
    
    // Hide after 3 seconds
    setTimeout(function() {
        copySuccess.classList.add("hidden");
    }, 3000);
}
</script>

<?php include 'views/layouts/footer.php'; ?>
