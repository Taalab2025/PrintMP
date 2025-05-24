<?php
/**
 * Review Form View
 * File path: views/pages/reviews/form.php
 * Review submission form for completed orders
 */

$language = $this->localization->getCurrentLanguage();
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 <?= $textAlign ?>">
                <?= $this->localization->t('reviews.write_review') ?>
            </h1>
            <p class="mt-2 text-gray-600 <?= $textAlign ?>">
                <?= $this->localization->t('reviews.share_experience') ?>
            </p>
        </div>

        <!-- Order Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 <?= $textAlign ?>">
                <?= $this->localization->t('reviews.order_details') ?>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">
                        <?= $this->localization->t('orders.order_number') ?>:
                    </span>
                    <p class="font-medium">#<?= $order['id'] ?></p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">
                        <?= $this->localization->t('orders.service') ?>:
                    </span>
                    <p class="font-medium">
                        <?= htmlspecialchars($service["title_{$language}"]) ?>
                    </p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">
                        <?= $this->localization->t('vendors.vendor') ?>:
                    </span>
                    <p class="font-medium">
                        <?= htmlspecialchars($vendor["company_name_{$language}"]) ?>
                    </p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">
                        <?= $this->localization->t('orders.total_amount') ?>:
                    </span>
                    <p class="font-medium">
                        <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <?php if ($this->session->getFlash('error')): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-red-600 <?= $textAlign ?>">
                        <?= htmlspecialchars($this->session->getFlash('error')) ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="POST" action="/orders/<?= $order['id'] ?>/review" class="space-y-6">
                <?= $this->session->getCSRFTokenField() ?>

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 <?= $textAlign ?>">
                        <?= $this->localization->t('reviews.rating') ?> *
                    </label>

                    <div class="flex items-center <?= $isRtl ? 'flex-row-reverse' : '' ?> mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button"
                                    class="star-rating text-2xl <?= $isRtl ? 'ml-1' : 'mr-1' ?> text-gray-300 hover:text-yellow-400 transition-colors cursor-pointer"
                                    data-rating="<?= $i ?>">
                                ★
                            </button>
                        <?php endfor; ?>
                    </div>

                    <input type="hidden" name="rating" id="rating" required>

                    <div id="rating-text" class="text-sm text-gray-500 <?= $textAlign ?>">
                        <?= $this->localization->t('reviews.select_rating') ?>
                    </div>

                    <?php if ($validationErrors = $this->session->getFlash('validation_errors')): ?>
                        <?php if (isset($validationErrors['rating'])): ?>
                            <p class="mt-1 text-sm text-red-600 <?= $textAlign ?>">
                                <?= htmlspecialchars($validationErrors['rating']) ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Comment -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2 <?= $textAlign ?>">
                        <?= $this->localization->t('reviews.comment') ?>
                    </label>

                    <textarea name="comment"
                              id="comment"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?= $textAlign ?>"
                              placeholder="<?= $this->localization->t('reviews.comment_placeholder') ?>"
                              maxlength="1000"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>

                    <div class="mt-1 text-sm text-gray-500 <?= $textAlign ?>">
                        <?= $this->localization->t('reviews.comment_optional') ?>
                    </div>

                    <?php if ($validationErrors && isset($validationErrors['comment'])): ?>
                        <p class="mt-1 text-sm text-red-600 <?= $textAlign ?>">
                            <?= htmlspecialchars($validationErrors['comment']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Guidelines -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h3 class="text-sm font-medium text-blue-800 mb-2 <?= $textAlign ?>">
                        <?= $this->localization->t('reviews.guidelines_title') ?>
                    </h3>
                    <ul class="text-sm text-blue-700 space-y-1 <?= $textAlign ?>">
                        <li>• <?= $this->localization->t('reviews.guideline_honest') ?></li>
                        <li>• <?= $this->localization->t('reviews.guideline_specific') ?></li>
                        <li>• <?= $this->localization->t('reviews.guideline_respectful') ?></li>
                        <li>• <?= $this->localization->t('reviews.guideline_relevant') ?></li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 <?= $isRtl ? 'sm:flex-row-reverse' : '' ?>">
                    <button type="submit"
                            id="submit-btn"
                            disabled
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <?= $this->localization->t('reviews.submit_review') ?>
                    </button>

                    <a href="/orders/<?= $order['id'] ?>"
                       class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors text-center">
                        <?= $this->localization->t('general.cancel') ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-rating.active {
    color: #fbbf24 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.getElementById('rating-text');
    const submitBtn = document.getElementById('submit-btn');

    const ratingTexts = {
        1: '<?= $this->localization->t('reviews.rating_1') ?>',
        2: '<?= $this->localization->t('reviews.rating_2') ?>',
        3: '<?= $this->localization->t('reviews.rating_3') ?>',
        4: '<?= $this->localization->t('reviews.rating_4') ?>',
        5: '<?= $this->localization->t('reviews.rating_5') ?>'
    };

    let currentRating = 0;

    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            setRating(rating);
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
    });

    document.querySelector('.star-rating').parentElement.addEventListener('mouseleave', function() {
        highlightStars(currentRating);
    });

    function setRating(rating) {
        currentRating = rating;
        ratingInput.value = rating;
        highlightStars(rating);
        ratingText.textContent = ratingTexts[rating];
        submitBtn.disabled = false;
    }

    function highlightStars(rating) {
        stars.forEach((star, index) => {
            const starNumber = parseInt(star.dataset.rating);
            if (starNumber <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    // Character count for comment
    const commentTextarea = document.getElementById('comment');
    const maxLength = 1000;

    function updateCharCount() {
        const remaining = maxLength - commentTextarea.value.length;
        const countElement = commentTextarea.parentElement.querySelector('.char-count');
        if (countElement) {
            countElement.textContent = `${remaining} <?= $this->localization->t('reviews.characters_remaining') ?>`;
        }
    }

    // Add character counter
    const charCountElement = document.createElement('div');
    charCountElement.className = 'char-count text-sm text-gray-500 mt-1 <?= $textAlign ?>';
    charCountElement.textContent = `${maxLength} <?= $this->localization->t('reviews.characters_remaining') ?>`;
    commentTextarea.parentElement.appendChild(charCountElement);

    commentTextarea.addEventListener('input', updateCharCount);
});
</script>
